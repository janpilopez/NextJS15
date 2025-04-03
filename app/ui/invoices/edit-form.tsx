'use client';

import { CustomerField, InvoiceForm } from '@/app/lib/definitions';
import {
  CheckIcon,
  ClockIcon,
  CurrencyDollarIcon,
  UserCircleIcon,
} from '@heroicons/react/24/outline';
import Link from 'next/link';
import { Button } from '@/app/ui/button';

import { State, updateInvoice } from '@/app/lib/action';

import { useActionState } from 'react';

export default function EditInvoiceForm({
  invoice,
  customers,
}: {
  invoice: InvoiceForm;
  customers: CustomerField[];
}) {
  //pruebas
                          // mismo contexto siemore, || se establece siempre pasar el id, ya que por reactjs no se puede pasar dorectamente explicaicon abajo
  const updateInvoiceWithId = updateInvoice.bind(null, invoice.id);
  const initialState: State = { message: null, errors: {} };
  const [state, formAction] = useActionState(updateInvoiceWithId, initialState);

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
              defaultValue={invoice.customer_id}
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
                defaultValue={invoice.amount}
                placeholder="Enter USD amount"
                className="peer block w-full rounded-md border border-gray-200 py-2 pl-10 text-sm outline-2 placeholder:text-gray-500"
              />
              <CurrencyDollarIcon className="pointer-events-none absolute left-3 top-1/2 h-[18px] w-[18px] -translate-y-1/2 text-gray-500 peer-focus:text-gray-900" />
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
                  defaultChecked={invoice.status === 'pending'}
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
                  defaultChecked={invoice.status === 'paid'}
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
        <Button type="submit">Edit Invoice</Button>
      </div>
    </form>
  );
}


// ¿Qué es una Acción del Servidor en Next.js?
// En Next.js, las acciones del servidor permiten ejecutar lógica en el servidor como parte de la respuesta de una solicitud. Estas se utilizan para realizar operaciones como:

// Actualizar una base de datos.
// Ejecutar lógica del servidor.
// Llamadas a APIs externas, etc.
// Las acciones del servidor se definen como funciones 'use server' y se pueden usar dentro de los formularios para realizar operaciones en el servidor.

// El Problema:
// Cuando intentas pasar un parámetro como id directamente en la acción del formulario de la siguiente manera:

// tsx
// Copy code
// <form action={updateInvoice(id)}>
// Esto no funcionará, ya que Next.js no puede recibir el valor directamente a través de esta sintaxis. El id no puede ser un argumento directo de la función de la acción del servidor.

// Solución: Usar bind para pasar el id correctamente.
// En lugar de pasar el id directamente como un argumento en el action del formulario, utilizamos el método bind de JavaScript para crear una versión de la función de la acción del servidor que ya tiene el id preconfigurado.

// ¿Qué hace .bind(null, invoice.id)?
// updateInvoice: Es la función que realizará la acción en el servidor (probablemente actualizando la factura en la base de datos).
// #TODO: BIND .bind(null, invoice.id): Esto es un truco de JavaScript. Lo que hace es crear una nueva versión de updateInvoice, pero con el primer argumento (en este caso, el id de la factura) ya predefinido. Es decir, estamos "ligando" (bind) el id a la función, lo que garantiza que updateInvoice ya reciba ese id cuando se ejecute.
// El null que pasamos en .bind(null, ...) es el valor del contexto (this), pero en este caso no lo necesitamos, por lo que pasamos null
// El primer parámetro de bind es el valor de this que se establece en la nueva función. En este caso, se pasa null, lo que significa que no se está especificando un valor para this, por lo que no se altera el contexto de la función.
// El segundo parámetro, invoice.id, es el primer argumento que se fijará en la nueva función. Esto significa que cada vez que llames a updateInvoiceWithId, automáticamente se pasará invoice.id como primer argumento, sin necesidad de pasarlo manualmente.

// Nota: Usar un campo de entrada oculto en su forma también funciona (por ejemplo. 
{/* <input type="hidden" name="id" value={invoice.id} />).
//  Sin embargo, los valores aparecerán como texto completo en la fuente HTML, que no es ideal para datos sensibles como ID. */}