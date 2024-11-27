// type se usa aquí para indicar que estamos importando solo el tipo de la configuración y no la implementación de NextAuth.
import type { NextAuthConfig } from 'next-auth';


export const authConfig = {
    pages: {
        signIn: '/login',
    },
    callbacks: {
        authorized({ auth, request: { nextUrl } }) {
            //La expresión !!auth?.user utiliza el operador de negación lógica (!) dos veces para convertir un valor en un booleano. Aquí te explico cómo funciona:
            // Desglose de !! 
            // Primer ! (Negación): // El primer ! convierte el valor en su opuesto booleano. Si auth?.user es truthy (es decir, si tiene un valor considerado verdadero, como un objeto, número distinto de cero, cadena no vacía, etc.), !auth?.user se convierte en false. Si es falsy (como null, undefined, 0, "", etc.), se convierte en true.
            // Segundo ! (Negación de la Negación): // El segundo ! invierte el resultado del primer !. Entonces, si auth?.user era truthy, el resultado final será true. Si era falsy, el resultado final será false.
            const isLoggedIn = !!auth?.user;  // Verifica si el usuario está autenticado
            const isOnDashboard = nextUrl.pathname.startsWith('/dashboard');  // Verifica si está intentando acceder al dashboard

            if (isOnDashboard) {
                if (isLoggedIn) return true;  // Si está autenticado y quiere acceder al dashboard, lo permite
                return false;  // Si no está autenticado, lo redirige al login
            } else if (isLoggedIn) {
                return Response.redirect(new URL('/dashboard', nextUrl));  // Si está autenticado y no está en el dashboard, lo redirige al dashboard
            }
            return true;  // Si no está autenticado y no está en el dashboard, permite el acceso a otras páginas
        },
    },

    providers: [],
} satisfies NextAuthConfig;

// Crear el archivo auth.config.ts:

// La idea es crear un archivo de configuración (auth.config.ts) que contenga la configuración personalizada de NextAuth.js. Este archivo va a exportar un objeto authConfig con las opciones necesarias.
// Exportar un objeto authConfig con la opción pages:

// El objeto authConfig contiene una propiedad llamada pages, que se utiliza para personalizar las rutas de páginas de NextAuth.js.
// En este caso, se está configurando la página de inicio de sesión (signIn) para que redirija a una ruta personalizada: /login.
// Configurar la opción pages:

// La opción pages en NextAuth.js te permite configurar rutas personalizadas para varias páginas predeterminadas que NextAuth.js gestiona de forma automática, tales como:
// signIn: Página de inicio de sesión.
// signOut: Página de salida (cuando el usuario cierra sesión).
// error: Página de error en caso de fallos en la autenticación.

// con pages.signIn
// lo que haces es decirle a NextAuth.js que cuando el sistema intente redirigir a la página de inicio de sesión (por ejemplo, cuando un usuario no autenticado intente acceder a una página protegida), en lugar de usar la página predeterminada de NextAuth.js para el inicio de sesión, se debe redirigir a la página /login de tu aplicación.