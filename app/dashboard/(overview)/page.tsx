import { Card } from "@/app/ui/dashboard/cards";
import RevenueChart from "@/app/ui/dashboard/revenue-chart";
import LatestInvoices from "@/app/ui/dashboard/latest-invoices";
//antes enviavamos de aqui la data con fecthRevenue y el chats solo marcaba el modelo, con suspense la data la gestiona el mismo componente revenuechart
// #TODO: SUSPENSE USO (2 MANERA DE HACER STREAMING)
import { Suspense } from "react";
import { 
  CardsSkeleton,
  LatestInvoicesSkeleton, 
  RevenueChartSkeleton 
} from "@/app/ui/skeletons";

import CardWrapper from "@/app/ui/dashboard/cards";

export default async function Page() {

  // const revenue = await fetchRevenue(); MANERA ANTIGUA DE CARGA BLOQUEADA
  // const latestInvoices = await fetchLatestInvoices();
  // const {
  //   numberOfCustomers,
  //   numberOfInvoices,
  //   totalPaidInvoices,
  //   totalPendingInvoices
  // } = await fetchCardData();

  return (
    <main>
      <h1 className={`mb-4 text-xl md:text-2xl`}>
        Dashboard
      </h1>
      <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <Suspense fallback={<CardsSkeleton/> }>
              <CardWrapper/>
          </Suspense>

      </div>
      <div className="mt-6 grid grid-cols-1 gap-6 md:grid-cols-4 lg:grid-cols-8">
        <Suspense fallback={<RevenueChartSkeleton/>} >
            {/* <RevenueChart revenue={revenue}  /> */}
            <RevenueChart/>
        </Suspense>
        <Suspense fallback={<LatestInvoicesSkeleton/>}>
            {/* <LatestInvoices latestInvoices={latestInvoices} /> */}
            <LatestInvoices />
        </Suspense>
        
      </div>
    </main>
  );
}
