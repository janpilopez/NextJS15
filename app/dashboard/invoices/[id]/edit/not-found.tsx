import Link from 'next/link';
import { FaceFrownIcon } from '@heroicons/react/24/outline';
 
export default function NotFound() {
  return (
    <main className="flex h-full flex-col items-center justify-center gap-2">
      <FaceFrownIcon className="w-10 text-gray-400" />
      <h2 className="text-xl font-semibold">404 Not Found</h2>
      <p>Could not find the requested invoice.</p>
      <Link
        href="/dashboard/invoices"
        className="mt-4 rounded-md bg-blue-500 px-4 py-2 text-sm text-white transition-colors hover:bg-blue-400"
      >
        Go Back
      </Link>
    </main>
  );
}
// La frase "notFound tendrá precedencia sobre error.tsx" significa que, en la jerarquía de errores, Next.js prioriza el manejo de un error 404 (no encontrado) sobre otros errores generales cuando ambos archivos existen en una misma ruta o carpeta.
// Resumen de Precedencia:
// notFound.tsx tendrá precedencia si se trata de un error 404 (cuando la página solicitada no existe).
// error.tsx se utilizará para manejar errores generales de la aplicación, pero no se usará para manejar los errores 404, ya que esos se gestionan con notFound.tsx.


// el errores.tsx se crear a nivel principal de la clearPreviewData, y el notfound a nivel de cada subcarpeta/ruta