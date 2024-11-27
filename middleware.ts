import NextAuth from 'next-auth';
import { authConfig } from './auth.config';

export default NextAuth(authConfig).auth;

export const config = {
    // https://nextjs.org/docs/app/building-your-application/routing/middleware#matcher
    matcher: ['/((?!api|_next/static|_next/image|.*\\.png$).*)'],
};
// Aquí estás inicializando NextAuth.js con la authConfigobjeto y exportación de authpropiedad. También estás usando el matcheropción de Middleware para especificar que debe funcionar en rutas específicas.
// La ventaja de emplear Middleware para esta tarea es que las rutas protegidas ni siquiera comenzarán a renderizar hasta que el Middleware verifique la autenticación, mejorando tanto la seguridad como el rendimiento de su aplicación.

// 1. Importación de NextAuth y la configuración personalizada
// typescript
// Copy code
// import NextAuth from 'next-auth';
// import { authConfig } from './auth.config';
// NextAuth: Es la función principal que se importa desde el paquete next-auth. Es responsable de manejar la autenticación de usuarios en una aplicación Next.js.
// authConfig: Importamos la configuración personalizada que definimos en el archivo auth.config.ts. Esta configuración contiene los detalles de cómo NextAuth debe comportarse en términos de páginas, proveedores, callbacks, etc.
// 2. Inicialización de NextAuth con la configuración personalizada
// typescript
// Copy code
// export default NextAuth(authConfig).auth;
// NextAuth(authConfig): Aquí estamos inicializando NextAuth con la configuración authConfig que definimos en el archivo anterior.
// El authConfig puede contener detalles como las páginas personalizadas (como la página de inicio de sesión), la lógica de callbacks para la autorización, los proveedores de autenticación, etc.
// .auth: Después de inicializar NextAuth, estamos accediendo a la propiedad .auth, que es la función de autenticación de NextAuth que manejará las solicitudes de autenticación, como el inicio de sesión y el cierre de sesión. Se expone a través de la configuración predeterminada de NextAuth, y al usar .auth estamos diciendo que solo nos importa la parte relacionada con la autenticación.
// En resumen, esta línea exporta una configuración de autenticación completamente personalizada para NextAuth, usando lo que definimos en authConfig.

// 3. Configuración del middleware (config)
// typescript
// Copy code
// export const config = {
//   // https://nextjs.org/docs/app/building-your-application/routing/middleware#matcher
//   matcher: ['/((?!api|_next/static|_next/image|.*\\.png$).*)'],
// };
// Este bloque exporta una configuración de middleware. El middleware es una función especial en Next.js que se ejecuta antes de que se sirva una solicitud a la página. Se utiliza para ejecutar lógica en cada solicitud que llega a tu aplicación, como redirecciones, autenticación, autorización, etc.

// matcher
// El matcher es una configuración de rutas que determina qué solicitudes deben pasar por el middleware. En este caso, está usando una expresión regular para definir qué rutas deben ser interceptadas por el middleware.

// /((?!api|_next/static|_next/image|.*\\.png$).*):
// (?!...): Esta es una expresión regular negativa (lookahead). Significa "no coincidir con lo que sigue".

// El patrón api|_next/static|_next/image|.*\\.png$ indica que no debe coincidir con las rutas que contienen:

// api: Las rutas que comienzan con /api.
// _next/static: Las rutas que comienzan con _next/static (archivos estáticos generados por Next.js).
// _next/image: Las rutas que comienzan con _next/image (archivos de imágenes optimizados por Next.js).
// .*\\.png$: Las rutas que terminan en .png (archivos de imágenes PNG).
// El resto de las rutas sí serán interceptadas por el middleware. Esto significa que el middleware se aplicará a todas las rutas excepto aquellas que coincidan con las rutas de la API, archivos estáticos o imágenes .png.

// Propósito del matcher
// El propósito del matcher es asegurarse de que el middleware se aplique solo a las rutas que no sean para API o archivos estáticos de Next.js. Esto es útil porque no queremos que el middleware de autenticación interfiera con las rutas que manejan archivos estáticos (como imágenes, JavaScript o CSS) o las rutas de la API, que generalmente no requieren autenticación.