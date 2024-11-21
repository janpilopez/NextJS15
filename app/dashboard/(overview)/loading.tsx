import React from "react";
// Algunas cosas están sucediendo aquí:

//     loading.tsxes un archivo especial Next.js construido sobre Suspense, le permite crear información de devolución para mostrar como reemplazo mientras se carga el contenido de la página.
//     Desde entonces <SideNav>es estática, se muestra inmediatamente. El usuario puede interactuar con <SideNav>mientras que el contenido dinámico se está cargando.
//     El usuario no tiene que esperar a que la página termine de cargar antes de navegar lejos (esto se llama navegación interrumpa).

// Felicidades. Acabas de implementar el streaming. Pero podemos hacer más para mejorar la experiencia del usuario. Mostremos un esqueleto de carga en lugar de Loading…texto.

// ##Ahora, el loading.tsxEl archivo solo se aplicará a su página de vista general del DASHBOARD Y NO A LOS CURSOTMER O INVOCIES.
import DashboardSkeleton from "../../ui/skeletons";
export default function Loading() {
  return <DashboardSkeleton></DashboardSkeleton>
}
// Los grupos de rutas le permiten organizar archivos en grupos lógicos sin afectar a la estructura de la ruta URL. 
// Cuando crea una nueva carpeta usando paréntesis (), el nombre no se incluirá en la ruta URL. 
// Así que /dashboard/(overview)/page.tsxse convierte /dashboard.
