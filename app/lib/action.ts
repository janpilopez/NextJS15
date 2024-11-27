"use server";
// import { sql } from "@vercel/postgres";
// Añadándome la 'use server', marca todas las funciones exportadas dentro del archivo como Acciones del servidor.
// Estas funciones del servidor pueden entonces ser importadas y usadas en componentes de Cliente y Servidor
// #TODO: ZOD LIBRERIA
import { z } from "zod";
import { createClient } from '@supabase/supabase-js';

import { revalidatePath } from 'next/cache';
import { redirect } from 'next/navigation';
import { signIn } from "@/auth";
import { AuthError } from "next-auth";



const supabase = createClient('https://dzblbuhwqjigieabdomi.supabase.co', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR6YmxidWh3cWppZ2llYWJkb21pIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzIwNDQxMjQsImV4cCI6MjA0NzYyMDEyNH0.4bV7yYtkM5hAvSc9TNklvS4zlyPrVTegmwAcBGj3d0g')

const FormSchema = z.object({
    id: z.string(),
    customerId: z.string({
        invalid_type_error: 'Please select a customer.',
    }),
    amount: z.coerce.number()
        .gt(0, { message: 'Please enter an amount greater than $0.' }),
    status: z.enum(["pending", "paid"], {
        invalid_type_error: 'Please select an invoice status.',
    }),
    date: z.string(),
});

//aqui almacenamos los errores en cadenas de array si hay varios
export type State = {
    errors?: {
        customerId?: string[];
        amount?: string[];
        status?: string[];
    };
    message?: string | null;
};

// Esta función genera un nuevo esquema basado en FormSchema, pero omite las propiedades id y date.
// Es decir, el nuevo esquema CreateInvoice será el mismo que FormSchema pero sin esas dos propiedades.
const CreateInvoice = FormSchema.omit({ id: true, date: true });

export async function createInvoice(
    prevState: State, 
    formData: FormData) {

    //con este formato definido automaticamente preparamos las propiedades en su formato correcto para ser insertadas en la BD
    // const { customerId, amount, status } = CreateInvoice.parse({
    const validatedFields = CreateInvoice.safeParse({
        // safeParse()devolvirá un objeto que contenga un success o error campo. Esto ayudará a manejar la validación con más gracia sin haber puesto esta lógica dentro de la try/catchbloques.
        customerId: formData.get("customerId"),
        amount: formData.get("amount"),
        status: formData.get("status"),
    })

    // Si validatedFields no tiene éxito, devolvemos la función temprano con los mensajes de error de Zod.
    if (!validatedFields.success) {
        console.log(validatedFields.error.flatten().fieldErrors);

        return {
            // .flatten() se utiliza para convertir estos errores anidados en una lista o una estructura de datos plana. 
            errors: validatedFields.error.flatten().fieldErrors,
            message: 'Missing Fields. Failed to Create Invoice.',
        };
    }
    // Valores de almacenamiento en cents.
    // Suele ser una buena práctica almacenar valores monetarios en centésitos en su base de datos para eliminar errores de punto flotante de JavaScript y asegurar una mayor precisión.
    // const amountInCents = amount*100;
    // como nosotros quitamos el /100 en las validaciones lo guardamos tal cual

    const { customerId, amount, status } = validatedFields.data;
    const amountInCents = amount;
    const date = new Date().toISOString().split('T')[0];

    try {
        const { error } = await supabase
            .from('invoices')
            .insert({
                customer_id: customerId, amount: amountInCents,
                status, date
            })
    } catch (error) {
        return {
            message: 'Database Error: Failed to Create Invoice.',
        };
    }

    revalidatePath('/dashboard/invoices');
    redirect('/dashboard/invoices');
    // TODO: revalidatePath: Revalidación de contenido estático: Si estás utilizando características como Incremental Static Regeneration (ISR) en Next.js, puedes configurar las páginas para que se regeneren en segundo plano después de cierto tiempo (por ejemplo, cada 60 segundos). revalidatePath se usa para disparar la revalidación de una página inmediatamente después de que se haya producido un cambio.
    // Caché de ruta específica: Next.js guarda en caché las páginas generadas estáticamente. Cuando modificas o agregas datos (como insertar una nueva factura en la base de datos), puedes usar revalidatePath para actualizar esa ruta en la caché y asegurarte de que la siguiente vez que alguien visite esa página, verá los datos más recientes.
    // Actualización de la caché: Esto significa que la página asociada a la ruta pasada como argumento será regenera y tendrá los datos más recientes sin necesidad de reconstruir toda la aplicación.
}

const UpdateInvoice = FormSchema.omit({ id: true, date: true });

export async function updateInvoice(
    // id: string, formData: FormData
    id: string,
    prevState: State,
    formData: FormData
) {
    //parseamos: significa que ya retorna true o false con los errores o data parseados
    const validatedFields = UpdateInvoice.safeParse({
        customerId: formData.get('customerId'),
        amount: formData.get('amount'),
        status: formData.get('status'),
    });

    if (!validatedFields.success) {
        return {
            errors: validatedFields.error.flatten().fieldErrors,
            message: 'Missing Fields. Failed to Update Invoice.',
        };
    }
    const { customerId, amount, status } = validatedFields.data;
    // const amountInCents = amount * 100;
    const amountInCents = amount;

    try {
        const { error } = await supabase
            .from('invoices')
            .update({
                customer_id: customerId, amount: amountInCents,
                status
            })
            .eq('id', id)
    } catch (error) {
        return { message: 'Database Error: Failed to Update Invoice.' };
    }

    revalidatePath('/dashboard/invoices');
    redirect('/dashboard/invoices');
}


export async function deleteInvoice(id: string) {
    // throw new Error('Failed to Delete Invoice');
    try {
        const { error } = await supabase
            .from('invoices')
            .delete()
            .eq('id', id)
    } catch (error) {
        return { message: 'Database Error: Failed to Update Invoice.' };
    }
    // await sql`DELETE FROM invoices WHERE id = ${id}`;
    revalidatePath('/dashboard/invoices');
    // .Ya que esta acción está siendo llamada en el /dashboard/invoicescamino,
    //  no necesitas llamar redirect. Llamando revalidatePathactivará una nueva solicitud de servidor y volverá a presentar la tabla.
}

export async function authenticate(
    prevState: string | undefined,
    formData: FormData,
) {
    try {
        await signIn('credentials', formData);
    } catch (error) {
        if (error instanceof AuthError) {
            switch (error.type) {
                case 'CredentialsSignin':
                    return 'Invalid credentials.';
                default:
                    return 'Something went wrong.';
            }
        }
        throw error;
    }
}