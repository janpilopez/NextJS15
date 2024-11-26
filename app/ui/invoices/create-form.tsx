'use client';

import { CustomerField } from '@/app/lib/definitions';
import Link from 'next/link';
import {
  CheckIcon,
  ClockIcon,
  CurrencyDollarIcon,
  UserCircleIcon,
} from '@heroicons/react/24/outline';
import { Button } from '@/app/ui/button';
import { createInvoice, State } from '@/app/lib/action';

import { useActionState } from 'react';

export default function Form({ customers }: { customers: CustomerField[] }) {
  //agregar validacion lado servisor con useActionState, el use cliente lo definimos para que se 
  const initialState: State = { message: null, errors: {} };
  const [state, formAction] = useActionState(createInvoice, initialState);


  return (
    <form action={formAction}>
      <div className="rounded-md bg-gray-50 p-4 md:p-6">
        {/* Customer Name */}
        <div className="mb-4">
          <label htmlFor="customer" className="mb-2 block text-sm font-medium">
            Choose customer
          </label>
          <div className="relative">
            <select
              id="customer"
              name="customerId"
              className="peer block w-full cursor-pointer rounded-md border border-gray-200 py-2 pl-10 text-sm outline-2 placeholder:text-gray-500"
              defaultValue=""
              // Usando aria-describedby, puedes asociar un mensaje de error con ese campo para que los usuarios de lectores de pantalla reciban la descripción de forma automática cuando el campo sea inválido
              aria-describedby="customer-error"
            >
              <option value="" disabled>
                Select a customer
              </option>
              {customers.map((customer) => (
                <option key={customer.id} value={customer.id}>
                  {customer.name}
                </option>
              ))}
            </select>
            <UserCircleIcon className="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-gray-500" />
          </div>
          <div id="customer-error" aria-live="polite" aria-atomic="true">
            {state.errors?.customerId &&
              state.errors.customerId.map((error: string) => (
                <p className="mt-2 text-sm text-red-500" key={error}>
                  {error}
                </p>
              ))}
          </div>
        </div>

        {/* Invoice Amount */}
        <div className="mb-4">
          <label htmlFor="amount" className="mb-2 block text-sm font-medium">
            Choose an amount
          </label>
          <div className="relative mt-2 rounded-md">
            <div className="relative">
              <input
                id="amount"
                name="amount"
                type="number"
                step="0.01"
                placeholder="Enter USD amount"
                className="peer block w-full rounded-md border border-gray-200 py-2 pl-10 text-sm outline-2 placeholder:text-gray-500"
              />
              <CurrencyDollarIcon className="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-gray-500 peer-focus:text-gray-900" />
            </div>
            <div id="customer-error" aria-live="polite" aria-atomic="true">
              {state.errors?.amount &&
                state.errors.amount.map((error: string) => (
                  <p className="mt-2 text-sm text-red-500" key={error}>
                    {error}
                  </p>
                ))}
            </div>
          </div>
        </div>

        {/* Invoice Status */}
        <fieldset>
          <legend className="mb-2 block text-sm font-medium">
            Set the invoice status
          </legend>
          <div className="rounded-md border border-gray-200 bg-white px-[14px] py-3">
            <div className="flex gap-4">
              <div className="flex items-center">
                <input
                  id="pending"
                  name="status"
                  type="radio"
                  value="pending"
                  className="h-4 w-4 cursor-pointer border-gray-300 bg-gray-100 text-gray-600 focus:ring-2"
                />
                <label
                  htmlFor="pending"
                  className="ml-2 flex cursor-pointer items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600"
                >
                  Pending <ClockIcon className="h-4 w-4" />
                </label>
              </div>
              <div className="flex items-center">
                <input
                  id="paid"
                  name="status"
                  type="radio"
                  value="paid"
                  className="h-4 w-4 cursor-pointer border-gray-300 bg-gray-100 text-gray-600 focus:ring-2"
                />
                <label
                  htmlFor="paid"
                  className="ml-2 flex cursor-pointer items-center gap-1.5 rounded-full bg-green-500 px-3 py-1.5 text-xs font-medium text-white"
                >
                  Paid <CheckIcon className="h-4 w-4" />
                </label>
              </div>
            </div>
            <div id="customer-error" aria-live="polite" aria-atomic="true">
              {state.errors?.status &&
                state.errors.status.map((error: string) => (
                  <p className="mt-2 text-sm text-red-500" key={error}>
                    {error}
                  </p>
                ))}
              {state.message &&
                  <p className="mt-2 text-sm text-red-500" key={state.message}>
                    {state.message }
                  </p>
              }
            </div>
          </div>
        </fieldset>
      </div>
      <div className="mt-6 flex justify-end gap-4">
        <Link
          href="/dashboard/invoices"
          className="flex h-10 items-center rounded-lg bg-gray-100 px-4 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-200"
        >
          Cancel
        </Link>
        <Button type="submit">Create Invoice</Button>
      </div>
    </form>
  );
}

// 1. ¿Por qué usamos 'use client' aquí?
// La directiva 'use client' es una característica de Next.js 13 y versiones posteriores, que indica que el componente en el que se encuentra debe ejecutarse en el lado del cliente.

// En Next.js, puedes tener componentes que se ejecutan en el servidor y otros que se ejecutan en el cliente. La directiva 'use client' le dice a Next.js que este componente específico debe renderizarse en el cliente, incluso si está dentro de una estructura de carpeta que normalmente podría ejecutarse en el servidor.

// Esto es útil cuando necesitas interactividad en el cliente, como el manejo de eventos, formularios, o actualización dinámica de la interfaz, lo cual no es posible con solo renderizado del lado del servidor.

// En tu código, estamos trabajando con un formulario interactivo que realiza una acción (como crear una factura), lo que implica interacción con el usuario, y por lo tanto debe ejecutarse en el lado del cliente.

// 2. ¿Qué hace useActionState?
// El hook useActionState se utiliza para gestionar el estado de una acción que se ejecuta en el servidor, pero que está siendo invocada desde un componente del cliente. En este caso, createInvoice es una acción del servidor, que probablemente inserta o actualiza una factura en la base de datos.

// Cuando usas useActionState(createInvoice, initialState), estás diciendo que el estado de la acción (state) y la función para invocar la acción (formAction) se deben gestionar a través de este hook. Esto te permite:

// state: Almacenar el estado de la acción (por ejemplo, si la factura se creó correctamente, si hubo un error, etc.).
// formAction: La función que se debe ejecutar cuando se envíe el formulario. Esta función invoca la acción del servidor createInvoice.
// 3. Flujo de trabajo: ¿Cómo funciona esto?
// El formulario se envía desde el cliente: El usuario completa el formulario (probablemente con detalles sobre la factura) y lo envía.

// useActionState invoca la acción del servidor: Cuando el formulario se envía, formAction (devuelto por el hook useActionState) se ejecuta, lo que a su vez invoca la acción del servidor createInvoice.

// Estado de la acción: Mientras createInvoice se ejecuta en el servidor, el estado de la acción (indicado por state) puede ser utilizado para manejar lo que sucede en el cliente (por ejemplo, si la factura fue creada correctamente o si hubo un error).

// Manejo de respuestas: En función de cómo se resuelva la acción (si tiene éxito o si falla), puedes actualizar la UI en el cliente, mostrando un mensaje de éxito o error.