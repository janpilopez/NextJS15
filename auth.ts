import NextAuth from 'next-auth';
import { authConfig } from './auth.config';
import Credentials from 'next-auth/providers/credentials';
import { z } from 'zod';

import { createClient } from '@supabase/supabase-js';
import  { User } from '@/app/lib/definitions';
import bcrypt from 'bcrypt';
// import { compare } from 'bcryptjs';

const supabase = createClient('https://dzblbuhwqjigieabdomi.supabase.co', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR6YmxidWh3cWppZ2llYWJkb21pIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzIwNDQxMjQsImV4cCI6MjA0NzYyMDEyNH0.4bV7yYtkM5hAvSc9TNklvS4zlyPrVTegmwAcBGj3d0g')
 
// ¿Por qué se utiliza Promise<User | undefined> como tipo de retorno?
// La función getUser retorna un solo usuario o undefined en caso de que no se encuentre el usuario con ese correo. El tipo Promise<User | undefined> significa que, cuando se resuelva la promesa, se obtendrá un valor que será un objeto User o undefined (si no se encuentra el usuario).
async function getUser(email: string): Promise<User | undefined> {
  try {
    const {data} = await supabase
        .from('users')
        .select('*')
        .eq('email', email)    

    //@ts-ignore
    return data[0] as User[];
  } catch (error) {
    console.error('Failed to fetch user:', error);
    throw new Error('Failed to fetch user.');
  }
}

export const { auth, signIn, signOut } = NextAuth({
    ...authConfig,
    //DEFINIMOS AQUI EL PROVIDER DE SOLO CREDENCIALES, NO UTILIZAMOS OTROS PROVEEDORES DE SESION COMO GOOGLE O GITHUB
    providers: [
        Credentials({
            async authorize(credentials) {
                const parsedCredentials = z
                    .object({
                        email: z.string().email(),
                        password: z.string().min(6)
                    })
                    .safeParse(credentials);

                    if (parsedCredentials.success) {
                        const { email, password } = parsedCredentials.data;
                        const user = await getUser(email);
                        if (!user) return null;
                        const passwordsMatch = await bcrypt.compare(password, user.password);
                        
                        if (passwordsMatch) return user;

                    }
                    console.log('Invalid credentials');
                    return null;
            }
        })
    ],
});