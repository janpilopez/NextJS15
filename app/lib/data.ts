import { sql } from '@vercel/postgres';

import { createClient } from '@supabase/supabase-js';
import {
  CustomerField,
  CustomersTableType,
  InvoiceForm,
  InvoicesTable,
  LatestInvoice,
  LatestInvoiceRaw,
  Revenue,
} from './definitions';
import { formatCurrency } from './utils';
import { log } from 'console';

// Lee las variables de entorno de tu archivo .env.local
// const SUPABASE_URL = process.env.SUPABASE_URL;
// const SUPABASE_SERVICE_ROLE_KEY = process.env.SUPABASE_SERVICE_ROLE_KEY;

const supabase = createClient('https://dzblbuhwqjigieabdomi.supabase.co', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImR6YmxidWh3cWppZ2llYWJkb21pIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MzIwNDQxMjQsImV4cCI6MjA0NzYyMDEyNH0.4bV7yYtkM5hAvSc9TNklvS4zlyPrVTegmwAcBGj3d0g')
// const supabase1 = createClient<Database>( process.env.SUPABASE_URL, process.env.SUPABASE_ANON_KEY)

//DEFINIMOS UN TIPO PARA QUE TS DETECTE LOS RESULTADOS DEVUELTOS POR SUPA
type InvoiceStatus = {
  paid: number | null; // o string, dependiendo de cómo manejes los montos
  pending: number | null; // o string
};
//SQL SUPABASE
export async function fetchRevenue() {
  try {
    console.log('Fetching revenue data...');
    await new Promise((resolve) => setTimeout(resolve, 3000));
    // const { data } = await supabase.from('revenue').select()
    const { data } = await supabase.from('revenue').select('*')
    console.log('Data fetch completed after 3 seconds.');

    return data || [];  // Aseguramos que nunca sea null, siempre será un arreglo.


  } catch (error) {
    console.error('Supabase Error:', error);
    throw new Error('Failed to fetch revenue data.');
  }
}

//##supabase
export async function fetchLatestInvoices() {
  try {
    const { data } = await supabase
    .from('invoices')
    .select(`
      id, amount,
      customers (name, image_url, email)
    `)
    .order('date', {ascending: false})
    .limit(5);
    
    const latestInvoices = (data || []).map((invoice) => ({
      ...invoice.customers,
      amount: formatCurrency(invoice.amount),
      id: invoice.id,
    }));
    //@ts-ignore #TODO: BUG DE TYPESCRIP NO RECONOCE LA DESESTRUCTURACION
    return latestInvoices as LatestInvoice[] || [];
  } catch (error) {
    console.error('Database Error:', error);
    throw new Error('Failed to fetch the latest invoices.');
  }
}


//1. EXPLICACION DEL CODIGO
//2. Obtener el Estado de las Facturas
// const invoiceStatusPromise = supabase
//   .from('invoices')
//   .select('status, amount')
// Objetivo: Aquí, se crea otra promesa (invoiceStatusPromise) que recupera el estado y el monto de cada factura de la tabla invoices.
// select('status, amount'): Esto indica que deseas obtener dos columnas: status (el estado de la factura, que puede ser 'paid' o 'pending') y amount (el monto de la factura).

// 3. Manejo de Resultados con Promesas
// .then(({ data, error }) => {
//   if (error) throw new Error(error.message);
// Objetivo: Una vez que se completa la consulta a la tabla invoices, se utiliza el método .then() para manejar el resultado.
// Desestructuración: Se desestructuran los resultados en data y error. data contendrá los registros obtenidos, mientras que error contendrá información sobre cualquier error que haya ocurrido durante la consulta.
// Manejo de Errores: Si hay un error, se lanza una nueva excepción con el mensaje de error, lo que detiene la ejecución y permite manejar el error en otra parte del código.

// 4. Calcular Totales
// const totals = data.reduce((acc, invoice) => {
//   if (invoice.status === 'paid') {
//     acc.paid += invoice.amount;
//   } else if (invoice.status === 'pending') {
//     acc.pending += invoice.amount;
//   }
//   return acc;
// }, { paid: 0, pending: 0 });
// Objetivo: Esta sección utiliza reduce para calcular el total de montos de las facturas pagadas y pendientes.
// data.reduce(...): reduce es un método de array que aplica una función a un acumulador y a cada valor de la matriz (en este caso, cada factura) para reducirlo a un solo valor.
// (acc, invoice): acc es el acumulador (un objeto que lleva el total de montos) y invoice es el elemento actual (cada factura).
// Condicionales:
// Si invoice.status === 'paid': Se suma el monto de la factura al total de paid.
// Si invoice.status === 'pending': Se suma el monto de la factura al total de pending.
// Valor Inicial: Se especifica { paid: 0, pending: 0 } como el valor inicial del acumulador acc.

// 5. Devolver Totales
// return totals; Objetivo: Finalmente, la función then devuelve el objeto totals, que contiene los totales acumulados de las facturas pagadas y pendientes.


export async function fetchCardData() {
  try {
    // Realiza las consultas a Supabase en paralelo
    const invoiceCountPromise = supabase
      .from('invoices')
      //es mas rapido que seleccionar todo
      .select('id', { count: 'exact' });

    const customerCountPromise = supabase
      .from('customers')
      .select('id', { count: 'exact' });

      const invoiceStatusPromise = supabase
      .from('invoices')
      .select('status, amount')
      .then(({ data, error }) => {
        if (error) throw new Error(error.message);
        //reduce recorre todos los elementos y devuelve solo uno (la sumatoria o final)
        //mientras eso pasa se usa su acumulador y se recorre cada elemento
        const totals = data.reduce((acc, invoice) => {
          if (invoice.status === 'paid') {
            acc.paid += invoice.amount;
          } else if (invoice.status === 'pending') {
            acc.pending += invoice.amount;
          }
          return acc;//retornamos acc porque es el acumulador(row(fila) final o sumado)
        }, { paid: 0, pending: 0 });
    
        return totals;//retornamos el valor final(que solo es uno)
      });


    const [invoiceCountResult, customerCountResult, invoiceStatusResult] = await Promise.all([
      invoiceCountPromise,//invoiceCountResult cada uno corresponde a cada uno de los parametros
      customerCountPromise,//customerCountResult
      invoiceStatusPromise,//invoiceStatusResult
    ]);
    
    // Extrae los datos
    const numberOfInvoices = Number(invoiceCountResult.count ?? '0');
    const numberOfCustomers = Number(customerCountResult.count ?? '0');
    
    // Asegúrate de que statusData tenga el formato correcto y maneja su valor
    const totalPaidInvoices = formatCurrency(invoiceStatusResult?.paid ?? 5);
    const totalPendingInvoices = formatCurrency(invoiceStatusResult?.pending ?? 50)


    return {
      numberOfCustomers,
      numberOfInvoices,
      totalPaidInvoices,
      totalPendingInvoices,
    };
  } catch (error) {
    console.error('Database Error:', error);
    throw new Error('Failed to fetch card data.');
  }
}
const ITEMS_PER_PAGE = 6;
export async function fetchFilteredInvoices(
  query: string,
  currentPage: number,
) {
  const offset = (currentPage - 1) * ITEMS_PER_PAGE;

  try {
    const invoices = await sql<InvoicesTable>`
      SELECT
        invoices.id,
        invoices.amount,
        invoices.date,
        invoices.status,
        customers.name,
        customers.email,
        customers.image_url
      FROM invoices
      JOIN customers ON invoices.customer_id = customers.id
      WHERE
        customers.name ILIKE ${`%${query}%`} OR
        customers.email ILIKE ${`%${query}%`} OR
        invoices.amount::text ILIKE ${`%${query}%`} OR
        invoices.date::text ILIKE ${`%${query}%`} OR
        invoices.status ILIKE ${`%${query}%`}
      ORDER BY invoices.date DESC
      LIMIT ${ITEMS_PER_PAGE} OFFSET ${offset}
    `;

    return invoices.rows;
  } catch (error) {
    console.error('Database Error:', error);
    throw new Error('Failed to fetch invoices.');
  }
}

export async function fetchInvoicesPages(query: string) {
  try {
    const count = await sql`SELECT COUNT(*)
    FROM invoices
    JOIN customers ON invoices.customer_id = customers.id
    WHERE
      customers.name ILIKE ${`%${query}%`} OR
      customers.email ILIKE ${`%${query}%`} OR
      invoices.amount::text ILIKE ${`%${query}%`} OR
      invoices.date::text ILIKE ${`%${query}%`} OR
      invoices.status ILIKE ${`%${query}%`}
  `;

    const totalPages = Math.ceil(Number(count.rows[0].count) / ITEMS_PER_PAGE);
    return totalPages;
  } catch (error) {
    console.error('Database Error:', error);
    throw new Error('Failed to fetch total number of invoices.');
  }
}

export async function fetchInvoiceById(id: string) {
  try {
    const data = await sql<InvoiceForm>`
      SELECT
        invoices.id,
        invoices.customer_id,
        invoices.amount,
        invoices.status
      FROM invoices
      WHERE invoices.id = ${id};
    `;

    const invoice = data.rows.map((invoice) => ({
      ...invoice,
      // Convert amount from cents to dollars
      amount: invoice.amount / 100,
    }));

    return invoice[0];
  } catch (error) {
    console.error('Database Error:', error);
    throw new Error('Failed to fetch invoice.');
  }
}

export async function fetchCustomers() {
  try {
    const data = await sql<CustomerField>`
      SELECT
        id,
        name
      FROM customers
      ORDER BY name ASC
    `;

    const customers = data.rows;
    return customers;
  } catch (err) {
    console.error('Database Error:', err);
    throw new Error('Failed to fetch all customers.');
  }
}

export async function fetchFilteredCustomers(query: string) {
  try {
    const data = await sql<CustomersTableType>`
		SELECT
		  customers.id,
		  customers.name,
		  customers.email,
		  customers.image_url,
		  COUNT(invoices.id) AS total_invoices,
		  SUM(CASE WHEN invoices.status = 'pending' THEN invoices.amount ELSE 0 END) AS total_pending,
		  SUM(CASE WHEN invoices.status = 'paid' THEN invoices.amount ELSE 0 END) AS total_paid
		FROM customers
		LEFT JOIN invoices ON customers.id = invoices.customer_id
		WHERE
		  customers.name ILIKE ${`%${query}%`} OR
        customers.email ILIKE ${`%${query}%`}
		GROUP BY customers.id, customers.name, customers.email, customers.image_url
		ORDER BY customers.name ASC
	  `;

    const customers = data.rows.map((customer) => ({
      ...customer,
      total_pending: formatCurrency(customer.total_pending),
      total_paid: formatCurrency(customer.total_paid),
    }));

    return customers;
  } catch (err) {
    console.error('Database Error:', err);
    throw new Error('Failed to fetch customer table.');
  }
}
