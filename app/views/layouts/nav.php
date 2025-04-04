
<?php
use app\models\User;
use webvimark\modules\UserManagement\UserManagementModule;
use webvimark\modules\UserManagement\components\GhostNav;
use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

$db =  $_SESSION['db'];

if($db != 'DB_Indicadores'):

    NavBar::begin([
        //'brandLabel' => Yii::$app->name,
        //'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    echo GhostNav::widget([
        'options' => ['class' => 'navbar-nav navbar-left nav'],
        'encodeLabels'=>false, // don't encode stuff in the label, needed for UserManagementModule::menuItems() 
        'items' => [
            [
                'label' => Yii::$app->name,'url' => Yii::$app->homeUrl, 
            ],
            [
                'label' => '<span class="glyphicon glyphicon-list-alt"></span> Gerenciales',
                'items' => [
                    
                    
                    // ['label' => 'Ventas', 'url' => ['/reportesventas/index' , 'menu'=>'report_ventas']],

                    [
                        'label' => 'Informes',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Cobus', 'url' => ['/dashboard/cobus']], 
                            ['label' => 'Financiero', 'url' => ['/dashboard/financiero']],
                            //['label' => '', 'url' => ['/dashboard/permisos']],
                        ]
                    ],
            
                ]
                
            ],
            [
                'label' => '<span class="glyphicon glyphicon-list"></span> Accesos',
                'items' => [
                    
                    
                    // ['label' => 'Ventas', 'url' => ['/reportesventas/index' , 'menu'=>'report_ventas']],
                
                    [
                        'label' => 'Maestro',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Visitantes', 'url' => ['/proveedores/index']], 
                            ['label' => 'Tipo Visitas', 'url' => ['/tipo-visitas/index']], 
                        ]
                    ],
                    [
                        'label' => 'Procesos',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Permisos Visitas', 'url' => ['/permisos-ingresos/index']], 
                            ['label' => 'Permisos Empleados Diarios', 'url' => ['/permisos/panelpermisos']],
                        ]
                    ],

                    [
                        'label' => 'Informes',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Visitas Individual', 'url' => ['/permisos-ingresos/veringresos']], 
                            ['label' => 'Visitas General', 'url' => ['/permisos-ingresos/veringresosgeneral']],
                        ]
                    ],
            
                ]
                
            ],
            [
                'label' => '<span class="glyphicon glyphicon-copy"></span> SSOO',
                'items' => [
                    [
                        'label' => 'Procesos',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Registro EPP Empleados', 'url' => ['/registro-epp/index']], 
                            ['label' => 'Incidentes Laborales', 'url' => ['/incidente/index']],     
                            ['label' => 'Incidentes Laborales', 'url' => ['/incidente/index']],     
                        ]
                    ],
                    [
                        'label' => 'Maestro',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'EPP', 'url' => ['/maestroepp/index']],     
                        ]
                    ],
            
                    [
                        'label' => 'Informes',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Listado EPP Vencimiento', 'url' => ['/registro-epp/listado']],     
                            ['label' => 'Listado Proyeccion EPP - Año', 'url' => ['/registro-epp/informe-epp-anio']],     
                            ['label' => 'Listado Proyeccion EPP Categoria- Año', 'url' => ['/registro-epp/informe-epp-categoria-anio']],     
                            ['label' => 'Listado Proyeccion EPP - Semana', 'url' => ['/registro-epp/informe-epp-semana']],     
                        ]
                    ],
            
                ]
                
            ],
            [
                'label' => '<span class="glyphicon glyphicon-plus-sign"></span> Médico',
                'items' => [
                    
                    
                    // ['label' => 'Ventas', 'url' => ['/reportesventas/index' , 'menu'=>'report_ventas']],
                    [
                        'label' => 'Maestros',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Turnos', 'url' => ['/turno-medico/index']],
                            ['label' => 'Patologías - Categorías ', 'url' => ['/patologia-categoria/index']],
                            ['label' => 'Patologías', 'url' => ['/patologia/index']],
                        ],
                    ],
                    
                    [
                        'label' => 'Procesos',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Consulta Médica', 'url' => ['/consulta-medica/index']],
                            ['label' => 'Ficha Médica Inicial', 'url' => ['/ficha-medica/index']],
                            ['label' => 'Ficha Ocupacional', 'url' => ['/ficha-ocupacional/index']],
                            ['label' => 'Certificado Médico', 'url' => ['/certificado-medico/index']],
                            ['label' => 'Certificado Salud Único', 'url' => ['/certificado-salud/index']],
                            
                        ]
                    ],
                    
                    [
                        'label' => 'Informes',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Consulta Médica', 'url' => ['consulta-medica/informe']],
                            ['label' => 'Morbilidad', 'url'=> ['consulta-medica/morbilidad']],
                            ['label' => 'Vencimiento Cert.Salud', 'url'=> ['/certificado-salud/certificadosvencidos']],
                            ['label' => 'Certificados Médicos', 'url'=> ['/certificado-medico/informe']]
                        ],
                    ],
                ]
                
            ],
            [
                'label' => '<span class="glyphicon glyphicon-cutlery"></span> Comedor',
                'items' => [
                    
                    
                    // ['label' => 'Ventas', 'url' => ['/reportesventas/index' , 'menu'=>'report_ventas']],
                    [
                        'label' => 'Maestros',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Horarios', 'url' => ['/comedor/index']],
                            ['label' => 'Carnét Visita', 'url' => ['/comedor-visitas/index']],
                        ],
                    ],
                    
                    [
                        'label' => 'Procesos',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Lunch', 'url' => ['/comedor/lunch']],
                            ['label' => 'Canasta Navideña', 'url' => ['/comedor/entregacanasta']],
                            ['label' => 'Marcación Manual', 'url' => ['/comedor/marcacionemanual']],
                            ['label' => 'Marcación Lote', 'url' =>['/comedor/cargarasistencialote']]
                        ]
                    ],
                    
                    [
                        'label' => 'Informes',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Lunch', 'url' => ['/comedor/infolunch']],
                            ['label' => 'Control Comedor', 'url' => ['/comedor/infolunch2']],
                            ['label' => 'Entrega Canastas', 'url' => ['/comedor/informecanasta']],
                            /*[
                                'label' => 'Lunchs Excel', 
                                'url' => User::hasRole('JEFECOSTOS') ? 'http://inforfish/ReportServer?%2FNomina%2FInformeComedor&rs%3AParameterLanguage=es-ES' : '#',
                                'linkOptions' => ['target' => '_blank']  ,
                                'template'=> '<a href="{url}" target="_blank">{label}</a>',                        
                            ],*/
                        ],
                    ],
            
                    
                    //  '<li class="divider"></li>',
                    // ['label' => 'Cash Manager(Produbanco)', 'url' => '#'],
                    
            ]],
            [
                'label' => '<span class="glyphicon glyphicon-check"></span> Módulo Nómina',
                'items' => [
                    
                    
                // ['label' => 'Ventas', 'url' => ['/reportesventas/index' , 'menu'=>'report_ventas']],
                    [
                        'label' => 'Maestros',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Bancos', 'url' => ['/bancos/index']],
                            ['label' => 'Conceptos Nómina', 'url' => ['/conceptos/index']],
                            ['label' => 'Centro de Costos', 'url' => ['/centro-costos/index']],
                            ['label' => 'Causa Salida', 'url' => ['/causa-salida/index']],
                            ['label' => 'Contratos', 'url' => ['/contratos/index']],
                            ['label' => 'Feriados', 'url' => ['/feriados/index']],
                            ['label' => 'Formas de Pago', 'url' => ['/forma-pago/index']],
                            ['label' => 'Horas Extras', 'url' => ['/horas-extras/index']],
                            ['label' => 'Empleados', 'url' => ['/empleados/index']],
                            ['label' => 'Entrega de Uniformes', 'url' => ['/empleados-uniforme/index']],
                            ['label' => 'Rentas(Rubros y Gastos)', 'url' => ['/rubros-gastos/index']],
                            ['label' => 'Tabla Rentas', 'url' => ['/impuesto-renta/index']],
                            ['label' => 'Canasta Básica', 'url' => ['/canasta-basica/index']],
                            ['label' => 'Tipos Permisos', 'url' => ['/tipo-permisos/index']],  
                            ['label' => 'Horarios', 'url' => ['/horarios/index']],  
                            ['label' => 'Empresas Servicios', 'url' => ['/empresa-servicios/index']],  
                            ['label' => 'Empleados Servicios', 'url' => ['/empresa-empleados/index']],  
                            ['label' => 'Mareas(Flota)', 'url' => ['/mareas/index']], 
                            ['label' => 'Eventos/Capacitaciones', 'url' => ['capacitaciones/index']] 
                            
                        
                        ],
                    ],

                    [
                        'label' => 'Procesos',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            [
                                'label' => 'Permisos',
                                'itemsOptions'=>['class'=>'dropdown-submenu'],
                                'submenuOptions'=>['class'=>'dropdown-menu'],
                                'items' => [
                                    ['label' => 'Permisos', 'url' => ['/permisos/index']],
                                    ['label' => 'Permisos por Lote', 'url' => ['/permisos/permisoslote']],
                                    ['label' => 'Permisos de Equipos', 'url' => ['/permisos-equipos/index']],
                                    ['label' => 'Entrada de Alimentos', 'url' => ['/permiso-alimento/index']],
                                ]
                            ],
                            [
                                'label' => 'Agendamiento',
                                'itemsOptions'=>['class'=>'dropdown-submenu'],
                                'submenuOptions'=>['class'=>'dropdown-menu'],
                                'items' => [
                                    ['label' => 'Grupos Agendamiento', 'url' => ['/cuadrillas/index']],
                                    ['label' => 'Agendamiento Laboral', 'url' => ['/agendamiento/index']],
                                ]
                            ],
                            [
                                'label' => 'Horas Extras',
                                'itemsOptions'=>['class'=>'dropdown-submenu'],
                                'submenuOptions'=>['class'=>'dropdown-menu'],
                                'items' => [
                                    ['label' => 'Asistencia', 'url' => ['/asistencia/index']],
                                    ['label' => 'Solicitud Horas Extras', 'url' => ['/soextras/index']],
                                    ['label' => 'Aprobación Solicitud Horas', 'url' => ['/soextras/aprobarsolicitudes']],
                                    ['label' => 'Revisión Solicitud Horas', 'url' => ['/soextras/revisarsolicitudes']],
                                ]
                            ],
                            [
                                'label' => 'Roles',
                                'itemsOptions'=>['class'=>'dropdown-submenu'],
                                'submenuOptions'=>['class'=>'dropdown-menu'],
                                'items' => [
                                    ['label' => 'Liquidación Periodos', 'url' => ['/rol/index']],
                                    ['label' => 'Novedades', 'url' => ['/novedades/index']],
                                    ['label' => 'Conjunto de Novedades', 'url' => ['/funciones/cargarnovedades']],
                                    ['label' => 'Archivo Banco', 'url' => ['/funciones/generatxt']],
                                ]
                            ],
                            [
                                'label' => 'Personal',
                                'itemsOptions'=>['class'=>'dropdown-submenu'],
                                'submenuOptions'=>['class'=>'dropdown-menu'],
                                'items' => [
                                    ['label' => 'Certificados Laborales', 'url' => ['/certificados-laborales/index']],
                                    ['label' => 'Vacaciones', 'url' => ['/vacaciones/index']],
                                    ['label' => 'Solicitud Vacaciones', 'url' => ['/solicitud-vacaciones/index']],
                                    ['label' => 'Solicitud Taxis', 'url' => ['/solicitud-taxis/index']],
                                    ['label' => 'Registro Gastos', 'url' =>['/gastos-proyectados/index']],
                                    ['label' => 'Préstamos Empresa', 'url'=> ['/prestamos/index']],
                                    ['label' => 'Actualización Datos', 'url' => ['/funciones/actualizaciondatos']],
                                    ['label' => 'Capacitaciones/Eventos', 'url' => ['/funciones/eventos']]
                                ]
                            ],
                            ['label' => 'Utilidades', 'url'=> ['/utilidades/index']],
                            ['label' => 'Finiquito', 'url'=> ['/finiquito/index']],
                            // ['label' => 'Ajustar Rol', 'url' => ['/rol-detalle/index']],
                            //['label' => 'Llamados de Atención', 'url'=>['/atencion/index']],    
                        ]
                    ],
                    
                    [
                        'label' => 'Informes',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            [
                                'label' => 'Asistencias',
                                'itemsOptions'=>['class'=>'dropdown-submenu'],
                                'submenuOptions'=>['class'=>'dropdown-menu'],
                                'items' => [
                                    ['label' => 'Asistencia por Empleado', 'url' => ['/asistencia/verasistencia']],
                                    ['label' => 'Asistencia General', 'url' => ['/asistencia/informeasistencia']],
                                    ['label' => 'Resumen Horas Laboradas', 'url' => ['/asistencia/resumen']],
                                    ['label' => 'Resumen Asistencia Detalle', 'url' => ['/asistencia/ausentismo']],
                                    ['label' => 'Resumen Asistencia Individual', 'url' => ['/asistencia/verasistenciahoras']],
                                ]
                            ],
                            [
                                'label' => 'Roles',
                                'itemsOptions'=>['class'=>'dropdown-submenu'],
                                'submenuOptions'=>['class'=>'dropdown-menu'],
                                'items' => [
                                    ['label' => 'Rol de Pagos (Detalle) ', 'url' => ['/funciones/roldetalle']],
                                    ['label' => 'Sobre Rol de Pago G.', 'url' => ['/funciones/imprimirroles']],
                                    ['label' => 'Sobre Rol de Pago I.', 'url' => ['/rol-usuario/index']],
                                    ['label' => 'Asiento Contable', 'url' => ['/funciones/asientocontable']],
                                ]
                            ],
                            [
                                'label' => 'Horas Extras',
                                'itemsOptions'=>['class'=>'dropdown-submenu'],
                                'submenuOptions'=>['class'=>'dropdown-menu'],
                                'items' => [
                                    ['label' => 'Horas Extras', 'url' => ['/asistencia/horasextras']],
                                    ['label' => 'Comparativo de Horas Extras', 'url' => ['/soextras/verhorasgeneradasvshorassolicitadas']],
                                    ['label' => 'Informe Horas Solicitadas', 'url' => ['/soextras/verhorasacumuladas']],
                                ]
                            ],
                            [
                                'label' => 'Personal',
                                'itemsOptions'=>['class'=>'dropdown-submenu'],
                                'submenuOptions'=>['class'=>'dropdown-menu'],
                                'items' => [
                                    ['label' => 'Informe Uso Comedor', 'url' => ['/asistencia/informeasistenciacomedor']],
                                    ['label' => 'Permisos Empleados', 'url' => ['/permisos/informepermisos']],
                                    ['label' => 'Salida de Personal', 'url' => ['/funciones/salidapersonal']],
                                    ['label' => 'Personal Activo', 'url' => ['/funciones/empleadosactivos']],
                                    ['label' => 'Informe Ajuste Salarial', 'url' => ['/funciones/ajustesalarial']],
                                    ['label' => 'Lista sueldo a pagar', 'url' => ['/funciones/infotipopago']],
                                    ['label' => 'Periodo Vacaciones', 'url' => ['/vacaciones/infoperiodovacaciones']],
                                    ['label' => 'Liquidacion Vacaciones', 'url' => ['/vacaciones/informegeneral']],
                                    ['label' => 'Credenciales Lote', 'url' => ['/empleados/credenciallote']],
                                    ['label' => 'Préstamos Empresa', 'url' => ['/prestamos/prestamos']],
                                    ['label' => 'Cumpleaños Empresarial', 'url' => ['/funciones/laborados']],
                                    ['label' => 'Cumpleaños', 'url' => ['/funciones/cumpleanios']],
                                    ['label' => 'Actualización Datos', 'url' =>['/funciones/datosanual']],
                                    ['label' => 'Eventos/Capacitaciones', 'url' =>['/funciones/informeeventos']]
                                ]
                            ],
                            //['label' => 'Informe Llamados Atencion', 'url' =>['/atencion/informeatencion']]
                        ],
                    ],
                    [
                        'label' => 'Funcionalidades',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Enviar Roles de pago', 'url' => ['/funciones/enviarroles']],
                            ['label' => 'Entrega Canastas', 'url' => ['/comedor/entregacanasta']],
                            ['label' => 'Marcaciones Manual', 'url' => ['/asistencia/marcacionemanual']],
                            //['label' => 'Permisos Visitas Diarios', 'url' => ['/permisos-ingresos/panelpermisos']],
                        ],
                    ],
                    [
                        'label' => 'Dashboard',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Empleados Activos', 'url' => ['/dashboard/index']],
                            ['label' => 'Producción', 'url' => ['/dashboard/edadproduccion']],
                            ['label' => 'Rotación Empleado', 'url' => ['/dashboard/rotacionempleado']],
                            ['label' => 'Personal Discapacidad', 'url' => ['/dashboard/personaldiscapacidad']],
                            ['label' => 'Tipo Contrato', 'url' => ['/dashboard/tipocontrato']], 
                            ['label' => 'Personal en Planta', 'url' => ['/dashboard/personalplanta']], 
                            ['label' => 'Personal Lactancia', 'url' => ['/dashboard/personalmaternidad']], 
                            ['label' => 'Horas Extras', 'url' => ['/dashboard/horasextras']], 
                            ['label' => 'Vacaciones', 'url' => ['/dashboard/vacaciones']], 
                            ['label' => 'Permisos', 'url' => ['/dashboard/permisos']], 
                        ],
                    ],
                //  '<li class="divider"></li>',
                // ['label' => 'Cash Manager(Produbanco)', 'url' => '#'],
                
                ]],  [
                    'label' => '<span class="glyphicon glyphicon-wrench"></span> Administración',
                    'items' => [
                        
                        
                        // ['label' => 'Ventas', 'url' => [' , 'menu'=>'report_ventas']],
                        [
                            'label' => 'Maestros',
                            'itemsOptions'=>['class'=>'dropdown-submenu'],
                            'submenuOptions'=>['class'=>'dropdown-menu'],
                            'items' => [
                                ['label' => 'Institución', 'url' => ['/empresas/index']],
                                ['label' => 'Nivel Organizacional', 'url' => ['/mandos/index']],
                                ['label' => 'Áreas', 'url' => ['/areas/index']],
                                ['label' => 'Departamentos', 'url' => ['/departamentos/index']],
                                ['label' => 'Cargos', 'url' => ['/cargos/index']],
                                ['label' => 'Actividades', 'url' => ['/actividades/index']],
                                ['label' => 'Centro de Costos', 'url' => ['/centro-costos/index']],
                                ['label' => 'Rutas Transporte', 'url' => ['/rutas-transporte/index']],
                                ['label' => 'Parámetro Password', 'url' => ['/configuracions/index']],
                                ['label' => 'Grupo Autorización', 'url' => ['/grupo-autorizacion/index']],
                                ['label' => 'Usuarios Departamentos', 'url' => ['/usuarios-departamentos/index']],
                                ['label' => 'Usuarios Permisos', 'url' => ['/usuarios-permisos/index']],
                                ['label' => 'Flujo de Autorización', 'url' => ['/flujo-autorizacion/index']],
                                ['label' => 'Flujo de Autorización Visitas', 'url' => ['/flujo-autorizacion-visitas/index']],
                                ['label' => 'Autorización Documentos', 'url' => ['/documento-autorizacion/index']],
                                ['label' => 'Parametrizar Sueldos', 'url' => ['/historial-sueldo/index']],
                                //['label' => 'Articulos Empresa', 'url' =>['/articulos/index']],
                            ],
                        ],
                        
            
                    ]],
            
            [
                'label' => '<span class="glyphicon glyphicon-lock"></span>',
                'items'=> UserManagementModule::menuItems(),
            ],
            Yii::$app->user->isGuest ?
            ['label' => 'Iniciar sesión', 'url' => ['/user-management/auth/login']] :
            ['label' => '<span class="glyphicon glyphicon-user"></span>', 
                
                'itemsOptions'=>['class'=>'dropdown-submenu'],
                'submenuOptions'=>['class'=>'dropdown-menu'],
                'items' => [
                    ['label' => Yii::$app->user->identity->username],
                    ['label' => 'Cerrar Sesión', 'url' => ['/user-management/auth/logout']],
                    ['label' => 'Cambiar Contraseña', 'url' => ['/user-management/auth/change-own-password']],
                ],
        
            ],
        ],
    ]);
    NavBar::end();
else:

    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    
    echo GhostNav::widget([
        'options' => ['class' => 'navbar-nav navbar-right nav'],
        'encodeLabels'=>false, // don't encode stuff in the label, needed for UserManagementModule::menuItems() 
        'items' => [
            [
                'label' => '<span class="glyphicon glyphicon-list-alt"></span> Formularios',
                'items' => [
                    
                    [
                        'label' => 'Procesos',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Sistemas', 'url' => ['/form-indicador-sistemas/index']],                        
                        ]
                    ],
                    
                ]
                
            ],
            [
                'label' => '<span class="glyphicon glyphicon-wrench"></span> Administración',
                'items' => [
                    
                    [
                        'label' => 'Maestros',
                        'itemsOptions'=>['class'=>'dropdown-submenu'],
                        'submenuOptions'=>['class'=>'dropdown-menu'],
                        'items' => [
                            ['label' => 'Indicadores', 'url' => ['/indicadores/index']],
                        ],
                    ],
                ]
            ],
            [
                'label' => '<span class="glyphicon glyphicon-user"></span> Admin',
                'items'=> UserManagementModule::menuItems(),
            ],
            Yii::$app->user->isGuest ?
            ['label' => 'Iniciar sesión', 'url' => ['/user-management/auth/login']] :
            ['label' => 'Cerrar sesión (' . Yii::$app->user->identity->username . ')', 
                
                'itemsOptions'=>['class'=>'dropdown-submenu'],
                'submenuOptions'=>['class'=>'dropdown-menu'],
                'items' => [
                    ['label' => 'Cerrar Sesión', 'url' => ['/user-management/auth/logout']],
                    ['label' => 'Cambiar Contraseña', 'url' => ['/user-management/auth/change-own-password']],
                ],
           
            ],
        ],
    ]);
    NavBar::end();

endif;
?>