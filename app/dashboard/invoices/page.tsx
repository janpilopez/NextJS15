import Pagination from '@/app/ui/invoices/pagination';
import Search from '@/app/ui/search';
import Table from '@/app/ui/invoices/table';
import { CreateInvoice } from '@/app/ui/invoices/buttons';
import { InvoicesTableSkeleton } from '@/app/ui/skeletons';
import { Suspense } from 'react';

import { fetchInvoicesPages } from '@/app/lib/data';
import { Metadata } from 'next';

// METADATA DEL COMPONENTE, SE SOBREPONE AL PADRE, NO RESPONSIVE
// export const metadata: Metadata = {
//   title: 'Invoices | Acme Dashboard',
// };

//RESPONSIVE
export const metadata: Metadata = {
  title: 'Invoices',
};

// #TODO: searchParams Page components accept a prop called searchParams, so you can pass the current URL params to the <Table> component.
export default async function Page(props: {
  searchParams?: Promise<{
    query?: string;
    page?: string;
  }>;
}) {

  const searchParams = await props.searchParams;
  const query = searchParams?.query || '';
  const currentPage = Number(searchParams?.page || 1);
  const totalPages = await fetchInvoicesPages(query);

  return (
    <div className="w-full">
      <div className="flex w-full items-center justify-between">
        <h1 className={`lusitana.className text-2xl`}>Invoices</h1>
      </div>
      <div className="mt-4 flex items-center justify-between gap-2 md:mt-8">
        <Search placeholder="Search invoices..." />
        <CreateInvoice />
      </div>
      {/* El key en Suspense se utiliza para identificar de manera única las "instancias" del Suspense. 
      // Cuando el valor de key cambia, React lo interpreta como que el componente ha cambiado y necesita ser re-renderizado. */}
      <Suspense key={query + currentPage} fallback={<InvoicesTableSkeleton />}>
        <Table query={query} currentPage={currentPage} />
      </Suspense>
      <div className="mt-5 flex w-full justify-center">
        <Pagination totalPages={totalPages} />
      </div>
    </div>
  );
}