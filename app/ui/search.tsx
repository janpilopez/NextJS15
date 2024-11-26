'use client';
import { MagnifyingGlassIcon } from '@heroicons/react/24/outline';

import { usePathname, useSearchParams, useRouter} from 'next/navigation';
import { useDebouncedCallback } from 'use-debounce';

export default function Search({ placeholder }: { placeholder: string }) {
  const searchParams = useSearchParams();
  const params = new URLSearchParams(searchParams);
  const pathName = usePathname(); 
  const { replace } = useRouter();//libreria de lado cliente

  //TODO: useDebouncedCallback Esta función envolverá el contenido de handleSearch, y sólo ejecute el código después de un tiempo específico una vez que el usuario ha dejado de escribir (300ms).
  const handleSearch = useDebouncedCallback( (term: string) => {
    //RESETEAMOS A PAGE 1 CUANDO EXISTE UNA BUSQUEDA SI NO SE QUEDARA EN LA ULTIMA O DIFERENTE A LA PAGINA 1 (ESUQE ES DONDE SALDRAN LOS RESULTADOS FILTRADOS)
    params.set('page', '1');
    //TODO: URLSearchParamses (seachParams) una API web que proporciona métodos de utilidad para manipular los parámetros de consulta de URL. En lugar de crear una cadena compleja literal, se puede usar para conseguir la cuerda de params como ?page=1&query=a.

    if (term) {
      //creamos query variable que es la que se mostrara como url con el valor a buscar
      //el page viene determinado por la paginacion de la base de datos***
      params.set('query', term);
    }else {
      params.delete('query');
    }
    alert(params.toString())
    // Ahora que tienes la cuerda de la consulta. Puedes usar Next.js's useRoutery usePathnameengancha para actualizar la URL.
    replace(`${pathName}?${params.toString()}`);
  }, 600);

  return (
    <div className="relative flex flex-1 flex-shrink-0">
      <label htmlFor="search" className="sr-only">
        Search
      </label>
      <input
        className="peer block w-full rounded-md border border-gray-200 py-[9px] pl-10 text-sm outline-2 placeholder:text-gray-500"
        placeholder={placeholder} onChange={ (e) => { handleSearch(e.target.value)} }
        defaultValue={searchParams.get('query')?.toString()}
      />
          {/* defaultValuevs. valueControlado vs. Descontrolado */}
          {/* Si estás usando el estado para administrar el valor de una entrada, usarías el valueatributo para convertirlo en un componente controlado. Esto significa que React gestionaría el estado de la entrada. */}
          {/* Sin embargo, ya que no estás usando el estado, puedes usar defaultValue. Esto significa que la entrada nativa manejará su propio estado. Esto está bien ya que estás guardando la consulta de búsqueda a la URL en lugar de estado. */}

      <MagnifyingGlassIcon className="absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-gray-500 peer-focus:text-gray-900" />
    </div>
  );
}
