import '@/app/ui/global.css';


export default function RootLayout({
  children,
}: {
  children: React.ReactNode;
  // children: React.ReactNode: En este caso, el tipo de children está explícitamente definido como React.ReactNode. Esto indica que children puede ser cualquier tipo de contenido válido que React pueda renderizar, como un string, un número, un componente React, un array de elementos, etc. React.ReactNode es un tipo muy amplio que cubre cualquier cosa que React pueda mostrar en la interfaz de usuario.
}) {
  return (
    <html lang="en">
      <body>{children}
      </body>
    </html>
  );
}
// ¿Qué es React.FC? // React.FC (o React.FunctionComponent)
// ¿Qué son las Props en React?
// En React, las props (abreviatura de "properties") son un mecanismo para pasar datos desde un componente padre a un componente hijo. Son valores o información que un componente recibe para personalizar su comportamiento o contenido.

// Propiedad: Es el dato que le pasas a un componente hijo.
// Componente Hijo: El componente que recibe la propiedad.
// Ejemplo básico de cómo usar props:

// tsx
// function Saludo({ nombre }: { nombre: string }) {
//   return <h1>Hola, {nombre}!</h1>;
// }

// export default function App() {
//   return <Saludo nombre="Juan" />;
// }
// En este ejemplo, Saludo es un componente hijo que recibe una propiedad nombre y la utiliza para mostrar un saludo. El componente App es el componente padre que pasa el valor "Juan" como prop al componente Saludo.

// 2. Explicación de la Sintaxis de Props con TypeScript
// ¿Qué significa la sintaxis ({ nombre }: { nombre: string })?
// Este es un patrón común cuando se utilizan props con TypeScript en un componente funcional de React. Vamos a desglosarlo paso a paso:

// tsx
// ({ nombre }: { nombre: string })
// { nombre }: Esto es una desestructuración de objetos en JavaScript y TypeScript. En lugar de acceder a props.nombre, puedes obtener directamente el valor de nombre de las props. Este es un atajo que permite acceder a las propiedades específicas del objeto props directamente.

// { nombre: string }: Esto es el tipado de TypeScript. Estás diciendo que la prop nombre debe ser de tipo string. Esto asegura que solo se pase un valor string a la propiedad nombre.

// Entonces, el código completo:

// tsx
// function Saludo({ nombre }: { nombre: string }) {
//   return <h1>Hola, {nombre}!</h1>;
// }
// Es equivalente a:

// tsx
// function Saludo(props: { nombre: string }) {
//   const { nombre } = props;  // Desestructuración de `props`
//   return <h1>Hola, {nombre}!</h1>;
// }
// 3. ¿Qué hace el : en la desestructuración?
// En TypeScript, el símbolo : se usa para especificar el tipo de datos. En el ejemplo anterior:

// tsx
// ({ nombre }: { nombre: string })
// El primer : se usa para desestructurar el objeto de las props y asignar la variable nombre a la propiedad nombre del objeto.
// El segundo : es donde especificamos el tipo de la prop: { nombre: string }. Le estamos diciendo a TypeScript que la prop nombre debe ser un string.
// 4. Tipado de Props en React con TypeScript
// En una aplicación React con TypeScript, es una buena práctica tipar las props que un componente espera recibir. Esto proporciona una verificación de tipos en tiempo de compilación, lo que ayuda a detectar errores antes de ejecutar la aplicación.

// Ejemplo básico de cómo se tipan las props:

// tsx
// interface SaludoProps {
//   nombre: string;
//   edad: number;
// }

// const Saludo: React.FC<SaludoProps> = ({ nombre, edad }) => {
//   return <h1>Hola, {nombre}. Tienes {edad} años.</h1>;
// };
// Aquí, usamos una interfaz (interface) llamada SaludoProps para definir el tipo de las props que el componente Saludo espera recibir.

// SaludoProps: Define que el componente Saludo espera dos props:

// nombre, que debe ser de tipo string.
// edad, que debe ser de tipo number.
// React.FC<SaludoProps>: React.FC es un tipo genérico para un componente funcional en React. Al usarlo, le estamos diciendo a TypeScript que el componente Saludo espera props que coincidan con el tipo SaludoProps.

// Desestructuración de Props: { nombre, edad } es un patrón de desestructuración. Esto significa que el objeto de props se desestructura directamente en las variables nombre y edad dentro del cuerpo del componente.

// Cómo se usa el componente:

// tsx
// function App() {
//   return <Saludo nombre="Juan" edad={30} />;
// }
// 5. Sintaxis Alternativa Usando Propiedades Directas
// En lugar de usar una interfaz, puedes tipar las props directamente en el componente como este ejemplo:

// tsx
// const Saludo = ({ nombre, edad }: { nombre: string, edad: number }) => {
//   return <h1>Hola, {nombre}. Tienes {edad} años.</h1>;
// };
// Aquí, estamos diciendo que Saludo recibe una prop de tipo { nombre: string, edad: number } directamente en la función del componente.

// 6. ¿Por qué usar TypeScript para las Props?
// TypeScript mejora el desarrollo en React de varias formas importantes:

// Verificación de tipos: Te asegura que las props pasadas al componente son del tipo esperado. Esto ayuda a evitar errores, como pasar un número cuando se espera una cadena de texto.

// Autocompletado: Los editores de código como VSCode pueden mostrar sugerencias de autocompletado cuando escribes código en componentes, lo que mejora la productividad.

// Documentación automática: Al tipar las props, generas una especie de "documentación" automática sobre qué props espera tu componente.

// Resumen
// Props en React son valores que se pasan de un componente padre a un componente hijo.
// En TypeScript, puedes tipar las props para asegurar que reciben los datos correctos.
// La sintaxis { nombre }: { nombre: string } es un ejemplo de desestructuración y tipado:
// Desestructuración: Extrae la propiedad nombre de las props.
// Tipado: { nombre: string } especifica que nombre debe ser un string.
// Si tienes más dudas o alguna pregunta adicional sobre este tema, ¡no dudes en preguntar!