import Form from '@/app/ui/invoices/edit-form';
import Breadcrumbs from '@/app/ui/invoices/breadcrumbs';
import { fetchInvoiceById, fetchCustomers } from '@/app/lib/data';
import { notFound } from 'next/navigation';

export default async function Page( props: { params: Promise<{ id: string }> }) {

    // params es un objeto que contiene los parámetros de la URL. En este caso, params.id es el id de la factura que se quiere editar.
    // params es una promesa, lo que significa que el valor de params no es inmediato y debe resolverse con await.
    const params = await props.params;
    const id = params.id;
    // params es una promesa que se resuelve para obtener el objeto { id: string } de los parámetros de la URL.
    // Se extrae el id del objeto resuelto, que es el identificador único de la factura que se va a editar.
    
    // Utiliza Promise.all para hacer dos peticiones asíncronas en paralelo:
    const [invoice, customers] = await Promise.all([
        fetchInvoiceById(id),
        fetchCustomers()
    ])


    
    if (!invoice) {
        notFound();
    }
    return (
        <main>
            <Breadcrumbs
                breadcrumbs={[
                    { label: 'Invoices', href: '/dashboard/invoices' },
                    {
                        label: 'Edit Invoice',
                        href: `/dashboard/invoices/${id}/edit`,
                        active: true,
                    },
                ]}
            />
            <Form invoice={invoice} customers={customers} />
        </main>
    );
}