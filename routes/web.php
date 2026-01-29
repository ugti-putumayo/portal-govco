<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Support\VideoEmbed;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransparenciaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SubmenuController;
use App\Http\Controllers\SubsubmenuController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\AssociationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LawController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UserModulePermissionController;
use App\Http\Controllers\SliderImageController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\ContracController;
use App\Http\Controllers\ContractorController;
use App\Http\Controllers\PlantOfficialController;
use App\Http\Controllers\EntitySettingController;
use App\Http\Controllers\LocateController;
use App\Http\Controllers\MipgController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\InstitutionalContentController;
use App\Http\Controllers\CitizenCareServicesController;
use App\Http\Controllers\EntityServiceController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\WebInformationController;
use App\Http\Controllers\CallsJobsController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ParticipateController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\PresupuestalController;
use App\Http\Controllers\EstadoFinancieroController;
use App\Http\Controllers\ContentPageController;
use App\Http\Controllers\ContentItemController;
use App\Http\Controllers\EntityInformationController;
use App\Http\Controllers\RegulationsController;
use App\Http\Controllers\HiringController;
use App\Http\Controllers\ProceduresServicesController;
use App\Http\Controllers\OpenDataController;
use App\Http\Controllers\private\consecutives\SeriesController;
use App\Http\Controllers\private\consecutives\ConsecutiveController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ChatController;

// Auth
Route::get('/login/google', [OAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/login/google/callback', [OAuthController::class, 'handleGoogleCallback']);
Route::get('/login/outlook', [OAuthController::class, 'redirectToOutlook'])->name('login.outlook');
Route::get('/login/outlook/callback', [OAuthController::class, 'handleOutlookCallback']);

// SOCIAL MEDIA
Route::get('/api/youtube/latest-embeddable', function () {

    return Cache::remember('youtube_latest_parsed', 600, function () {
        $apiKey = config('services.youtube.key');
        $channelId = 'UC1z-DVKIR_TJk1fMUDZx7Vw';

        $response = Http::get('https://www.googleapis.com/youtube/v3/search', [
            'key' => $apiKey,
            'channelId' => $channelId,
            'part' => 'snippet',
            'order' => 'date',
            'maxResults' => 1,
            'type' => 'video',
        ]);

        if (!$response->successful()) return null;

        $items = $response->json('items');
        if (empty($items)) return null;

        $videoId = $items[0]['id']['videoId'];
        $videoTitle = $items[0]['snippet']['title'];

        $rawUrl = "https://www.youtube.com/watch?v=" . $videoId;
        $embedData = VideoEmbed::parse($rawUrl, $videoTitle);

        return $embedData ? $embedData['src'] : null;
    });
});

// LANGUAGES
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['es', 'en', 'zh_CN'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// ROUTES PUBLIC PUTUMAYO
// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Content home
Route::get('/publications/all', [HomeController::class, 'indexPublicPublicationAll'])->name('publicationsAll');
Route::get('/publications/{id}', [HomeController::class, 'showPublicPublicationByType'])->name('publications.show');
Route::get('/publications', [HomeController::class, 'indexPublicPublications'])->name('publications');

// Menus
Route::get('/menu/{id}', [MenuController::class, 'show'])->name('menu.show');
Route::get('/submenu/{id}', [SubmenuController::class, 'show'])->name('submenu.show');
Route::get('/subsubmenu/{id}', [SubsubmenuController::class, 'show'])->name('subsubmenu.show');
Route::get('/transparency', [TransparenciaController::class, 'index'])->name('transparencia.index');
Route::get('/transparency/{id}', [TransparenciaController::class, 'show'])->name('transparencia.show');
Route::get('/subelement/{id}', [SidebarController::class, 'show'])->name('subelemento.show');
Route::get('/navbar', [MenuController::class, 'index']);

// Utils
Route::get('/entity/settings', [EntitySettingController::class, 'entitysettings'])->name('entitysettings');
Route::get('/locates', [LocateController::class, 'index'])->name('locates');
Route::get('/locates/cities/{department_id}', [LocateController::class, 'getCities']);
Route::get('/set-locale/{lang}', [App\Http\Controllers\LanguageController::class, 'setLocale'])->name('setLocale');

// Transparency Menu
// Información Entidad
Route::get('/mision', [EntityInformationController::class, 'indexPublicMision'])->name('mision');
Route::get('/structure', [WebInformationController::class, 'index'])->name('structure'); // PENDING
Route::get('/directory-institutional', [EntityInformationController::class, 'indexPublicDirectoryInstitutional'])->name('directory');
Route::get('/directory-active', [EntityInformationController::class, 'directory'])->name('directory-personal');
Route::get('/entities-directory', [EntityInformationController::class, 'indexPublicEntitiesDirectory'])->name('entities_directory');
Route::get('/associations-directory', [EntityInformationController::class, 'indexPublicAssociationDirectory'])->name('associations_directory');
Route::get('/public-service', [WebInformationController::class, 'index'])->name('public-service'); // PENDING
Route::get('/decision-making-directory', [EntityInformationController::class, 'indexPublicDecisionMakingDirectory'])->name('decision_making_directory');
Route::get('/events', [EntityInformationController::class, 'indexPublicCalendar'])->name('calendar');
Route::get('/control-entities', [EntityInformationController::class, 'indexPublicControlEntities'])->name('control_entities');
Route::get('/decisions', [WebInformationController::class, 'index'])->name('decisions');
Route::get('/resume', [EntityInformationController::class, 'indexPublicResume'])->name('resume');

Route::get('/presupuestal', [PresupuestalController::class, 'index'])->name('presupuestal.index');
Route::get('/presupuestal/{year}', [PresupuestalController::class, 'showByYear'])->name('presupuestal.year');
Route::get('/estados-financieros', [EstadoFinancieroController::class, 'index'])->name('estados-financieros.index');
Route::get('/estados-financieros/{year}', [EstadoFinancieroController::class, 'showByYear'])->name('estados-financieros.year');

Route::get('/internal-management-control/tab/{slug}', [ContentPageController::class, 'internalManagementControlTab'])->name('internal-management-control.tab');
Route::get('/internal-management-control', [ContentPageController::class, 'internalManagementControlContent'])->name('internal-management-control');

// Normativa
Route::get('/laws', [RegulationsController::class, 'indexPublicLaws'])->name('laws');
Route::get('/regulations', [WebInformationController::class, 'index'])->name('regulations'); // PENDING
Route::get('/program-plans', [RegulationsController::class, 'indexPublicProgramPlan'])->name('program_plans');
Route::get('/regulatory/agenda', [RegulationsController::class, 'indexPublicRegulatoryAgenda'])->name('regulatoryagenda');
Route::get('/regulatory/decree', [RegulationsController::class, 'indexPublicRegulatoryDecree'])->name('regulatorydecree');
Route::get('/regulatory-projects', [WebInformationController::class, 'index'])->name('regulatory-projects'); // PENDING
Route::get('/response-document-comments', [WebInformationController::class, 'index'])->name('response-document-comments'); // PENDING
Route::get('/needs/diagnosis', [RegulationsController::class, 'indexPublicNeedDiagnosis'])->name('needsdiagnosis');
Route::get('/anticorruption/hotline', [RegulationsController::class, 'indexPublicAnticorruptionHotline'])->name('anticorruptionhotline');
Route::get('/women', [RegulationsController::class, 'indexPublicWomen'])->name('womens'); 

// Contratación
Route::get('/paa', [WebInformationController::class, 'index'])->name('paa'); // PENDING
Route::get('/contractual/export', [ContractorController::class, 'exportContractors'])->name('dashboard.contractual.export');
Route::get('/contractual', [HiringController::class, 'indexPublicContractual'])->name('contractual');
Route::get('/execution', [HiringController::class, 'indexPublicExecution'])->name('execution');
Route::get('/hiring/anual', [HiringController::class, 'indexPublicHiringAnual'])->name('hiringanual');
Route::get('/formats/contracs', [WebInformationController::class, 'index'])->name('formatcontracs'); // PENDING

// Planeación, Presupuesto e Informes
Route::get('/general-budget', [WebInformationController::class, 'index'])->name('general-budget'); // PENDING
Route::get('/budget-execution', [WebInformationController::class, 'index'])->name('budget-execution'); // PENDING
Route::get('/plan-action', [TransparenciaController::class, 'planAction'])->name('plan-action');
Route::prefix('plan-action')->name('plan_action.')->group(function () {
    // 1. PINAR (Institutional Archives Plan)
    Route::get('/institutional-archives', [TransparenciaController::class, 'institutionalArchivesPlan'])->name('institutional_archives');
    // 1. PT (Transparency Plan)
    Route::get('/transparency-plan', [TransparenciaController::class, 'transparencyPlan'])->name('transparency_plan');
    // 2. PAA (Annual Acquisitions Plan)
    Route::get('/annual-acquisitions', [TransparenciaController::class, 'annualAcquisitionsPlan'])->name('annual_acquisitions');
    // 3. Plan Anual de Vacantes (Annual Vacancies Plan)
    Route::get('/annual-vacancies', [TransparenciaController::class, 'annualVacanciesPlan'])->name('annual_vacancies');
    // 4. Plan de Previsión de Recursos Humanos (HR Forecasting Plan)
    Route::get('/hr-forecasting', [TransparenciaController::class, 'hrForecastingPlan'])->name('hr_forecasting');
    // 5. Plan Estratégico de Talento Humano (Strategic Human Talent Plan)
    Route::get('/strategic-human-talent', [TransparenciaController::class, 'strategicHumanTalentPlan'])->name('strategic_human_talent');
    // 6. Plan Institucional de Capacitación (Institutional Training Plan)
    Route::get('/institutional-training', [TransparenciaController::class, 'institutionalTrainingPlan'])->name('institutional_training');
    // 7. Plan de Incentivos Institucionales (Institutional Incentives Plan)
    Route::get('/institutional-incentives', [TransparenciaController::class, 'institutionalIncentivesPlan'])->name('institutional_incentives');
    // 8. SST (Occupational Health and Safety Plan)
    Route::get('/occupational-health-safety', [TransparenciaController::class, 'occupationalHealthSafetyPlan'])->name('occupational_health_safety');
    // 9. Plan Anticorrupción (Anti-Corruption and Citizen Service Plan)
    Route::get('/anti-corruption-citizen-service', [TransparenciaController::class, 'antiCorruptionPlan'])->name('anti_corruption');
    // 10. PETI (IT Strategic Plan)
    Route::get('/it-strategic', [TransparenciaController::class, 'itStrategicPlan'])->name('it_strategic');
    // 11. Riesgos de Seguridad (Security and Privacy Risk Treatment Plan)
    Route::get('/security-privacy-risk-treatment', [TransparenciaController::class, 'riskTreatmentPlan'])->name('risk_treatment');
    // 12. Seguridad y Privacidad (Information Security and Privacy Plan)
    Route::get('/information-security-privacy', [TransparenciaController::class, 'securityPrivacyPlan'])->name('security_privacy');
});
Route::get('/invesment-projects', [WebInformationController::class, 'index'])->name('invesment-projects'); // PENDING
Route::get('/splice-reports', [WebInformationController::class, 'index'])->name('splice-reports'); // PENDING
Route::get('/public-information', [WebInformationController::class, 'index'])->name('public-information'); // PENDING
Route::get('/management-evaluation-audit-reports', [WebInformationController::class, 'index'])->name('management-evaluation-audit-reports'); // PENDING
Route::get('/fiscal-framework', [TransparenciaController::class, 'fiscalFramework'])->name('fiscal-framework');
Route::get('/reports-access', [WebInformationController::class, 'index'])->name('reports-access'); // PENDING

// TRÁMITES Y SERVICIOS -- PROCEDURES AND SERVICES
Route::get('/services-entity', [ProceduresServicesController::class, 'indexPublicServicesEntity'])->name('services-entity');
Route::get('/forms-procedures-services', [WebInformationController::class, 'index'])->name('forms-procedures-services'); // PENDING

// Datos Abiertos
Route::get('/information-assets-records', [WebInformationController::class, 'index'])->name('information-assets-records'); // PENDING
Route::get('/index-classified-information', [WebInformationController::class, 'index'])->name('index-classified-information'); // PENDING
Route::get('/information-publication-schema', [WebInformationController::class, 'index'])->name('information-publication-schema'); // PENDING
Route::get('/costs-public-information', [WebInformationController::class, 'index'])->name('costs-public-information'); // PENDING
Route::get('/open-data', [WebInformationController::class, 'index'])->name('open-data'); // PENDING
Route::get('/statistical-information-management', [OpenDataController::class, 'indexPublicStatisticalInformationManagement'])->name('transparency.statisticals')->defaults('slug', 'gestion-informacion-estadistica');
Route::get('/statistical-information-management/{slug?}', [OpenDataController::class, 'indexPublicStatisticalInformationManagement'])->name('transparency.statisticals')->defaults('slug', 'gestion-informacion-estadistica');

// Información Específica para Grupos de Interés
Route::get('/information-children-adolescents', [WebInformationController::class, 'index'])->name('information-children-adolescents'); // PENDING
Route::get('/information-women', [WebInformationController::class, 'index'])->name('information-women'); // PENDING
Route::get('/information-other-stakeholders', [WebInformationController::class, 'index'])->name('information-other-stakeholders'); // PENDING

// Reporte de Información Específica
Route::get('/agreements', [WebInformationController::class, 'index'])->name('agreements'); // PENDING
Route::get('/manual-functions-competencies', [WebInformationController::class, 'index'])->name('manual-functions-competencies'); // PENDING
Route::get('/management-goals-indicators', [WebInformationController::class, 'index'])->name('management-goals-indicators'); // PENDING
Route::get('/calls', [CallsJobsController::class, 'index'])->name('callsjobs');
Route::get('/calls/{id}', [CallsJobsController::class, 'show'])->name('callsjobs.show');
Route::get('/performance-evaluation', [WebInformationController::class, 'index'])->name('performance-evaluation'); // PENDING

// Información Tributaria
Route::get('/local-revenue', [WebInformationController::class, 'index'])->name('local-revenue'); // PENDING
Route::get('/rates-ica', [WebInformationController::class, 'index'])->name('rates-ica'); // PENDING

// GOBERNACIÓN
Route::get('/organization-chart', [GovernorateController::class, 'indexPublicOrganizationChart'])->name('organigrama');
Route::get('/cabinet/{typeCharge}', [GovernorateController::class, 'publicGovernorIndex'])->name('cabinet.index');
Route::get('/cabinet/{typeCharge}/{id}', [GovernorateController::class, 'publicGovernorShow'])->name('cabinet.show');
Route::get('/document-retention-table', [WebInformationController::class, 'otherIndex'])->name('document-retention-table'); // PENDING
Route::get('/institutional/contents', [InstitutionalContentController::class, 'publicIndex'])->name('institutional.contents');

Route::prefix('microsite-treasury')->group(function () {
    Route::get('/', [GovernorateController::class, 'indexPublicMicrositeTreasury'])->name('microsite-treasury.index');
    Route::get('/about', [GovernorateController::class, 'aboutPublicMicrositeTreasury'])->name('microsite-treasury.about');
    Route::get('/contact', [GovernorateController::class, 'contactPublicMicrositeTreasury'])->name('microsite-treasury.contact');
    Route::get('/slider', [GovernorateController::class, 'sliderPublicMicrositeTreasury'])->name('microsite-treasury.slider');
    Route::get('/audit', [GovernorateController::class, 'auditPublicMicrositeTreasury'])->name('microsite-treasury.fiscalizacion');
    Route::post('/audit/store', [GovernorateController::class, 'storeAudit'])->name('audit.store');
});

Route::get('/goverment-calls', [CallsJobsController::class, 'indexGoverment'])->name('goverment-calls');
Route::get('/record-call-jobs', [CallsJobsController::class, 'recordCallJobs'])->name('record-call-jobs');
Route::get('/secop', [WebInformationController::class, 'otherIndex'])->name('secop'); // PENDING
Route::get('/infraestructure', [WebInformationController::class, 'otherIndex'])->name('infraestructure'); // PENDING
Route::get('/competitiveness', [WebInformationController::class, 'otherIndex'])->name('competitiveness'); // PENDING
Route::get('/planning', [GovernorateController::class, 'planningSecretariat'])->name('planning'); // PENDING
Route::get('/social', [WebInformationController::class, 'otherIndex'])->name('social'); // PENDING
Route::get('/agriculture', [WebInformationController::class, 'otherIndex'])->name('agriculture'); // PENDING

Route::get('/sig', [GovernorateController::class, 'integratedManagementSystem'])->name('sig'); // PENDING

// ATENCIÓN Y SERVICIOS A LA CIUDADANÍA
Route::get('/judicial-notices', [CitizenCareServicesController::class, 'indexPublicJudicialNotices'])->name('judicial_notices.index');
Route::get('/entity-services', [CitizenCareServicesController::class, 'indexPublicEntityServices'])->name('entity-services');
Route::get('/pqrds-reports', [CitizenCareServicesController::class, 'indexPublicPqrdsReport'])->name('pqrds-reports');
Route::get('/pqrds/external', [CitizenCareServicesController::class, 'reportPqrds'])->name('pqrds.external'); // API GesDoc
Route::get('/user-satisfaction-reports/{slug?}', [CitizenCareServicesController::class, 'userSatisfactionReport'])->name('user-satisfaction-reports');
Route::get('/calendar', [AppointmentController::class, 'showCalendar'])->name('appointments.calendar');
Route::get('/form', [AppointmentController::class, 'showForm'])->name('appointments.form');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/data-protection', [CitizenCareServicesController::class, 'indexPublicDataProtection'])->name('data_protection');
Route::get('/site-map', [WebInformationController::class, 'otherIndex'])->name('site-map'); // PENDING

// PARTCIPATE
Route::get('/participate', [ParticipateController::class, 'index'])->name('participate.index');
Route::get('/participate/{id}', [ParticipateController::class, 'show'])->name('participate.show');

// NOTICIAS
Route::get('/last-news', [WebInformationController::class, 'otherIndex'])->name('last-news'); // PENDING
Route::get('/news-calls', [CallsJobsController::class, 'lastNewsIndex'])->name('newscallsjobs');
Route::get('/news-calls/{id}', [CallsJobsController::class, 'lastNewsShow'])->name('newscallsjobs.show');

// CONTACTO
Route::get('/directory', [ContactController::class, 'indexPublicContact'])->name('directory');
Route::get('/headquarters', [ContactController::class, 'indexPublicLocation'])->name('headquarters');


Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/chat/{user}/clear', [ChatController::class, 'clearChat']);
    Route::get('/chat', [ChatController::class, 'index'])->name('dashboard.chat.index');
    Route::get('/chat/{user}', [ChatController::class, 'fetchMessages'])->name('dashboard.chat.fetch');
    Route::post('/chat/{user}', [ChatController::class, 'sendMessage'])->name('dashboard.chat.send');
    
    Route::get('/dependencies/all', [DependencyController::class, 'getDependencies'])->name('dependenciesAll');
    Route::resource('dependencies', DependencyController::class)->names([
        'index'   => 'dashboard.dependencies.index',
        'create'  => 'dashboard.dependencies.create',
        'store'   => 'dashboard.dependencies.store',
        'show'    => 'dashboard.dependencies.show',
        'edit'    => 'dashboard.dependencies.edit',
        'update'  => 'dashboard.dependencies.update',
        'destroy' => 'dashboard.dependencies.destroy'
    ]);

    Route::put('/users/{user}/password', [UserController::class, 'updatePassword'])->name('dashboard.users.password.update');
    Route::get('/users/bosses', [UserController::class, 'getBossesArea'])->name('bosses');
    Route::resource('users', UserController::class)->names([
        'index'   => 'dashboard.users.index',
        'create'  => 'dashboard.users.create',
        'store'   => 'dashboard.users.store',
        'show'    => 'dashboard.users.show',
        'edit'    => 'dashboard.users.edit',
        'update'  => 'dashboard.users.update',
        'destroy' => 'dashboard.users.destroy'
    ]);

    Route::resource('roles', RolController::class)->names([
        'index'   => 'dashboard.roles.index',
        'create'  => 'dashboard.roles.create',
        'store'   => 'dashboard.roles.store',
        'show'    => 'dashboard.roles.show',
        'edit'    => 'dashboard.roles.edit',
        'update'  => 'dashboard.roles.update',
        'destroy' => 'dashboard.roles.destroy'
    ]);

    Route::get('/permissions/routes', [PermissionController::class, 'routes'])->name('dashboard.permissions.routes');
    Route::resource('permissions', PermissionController::class)->names([
        'index'   => 'dashboard.permissions.index',
        'create'  => 'dashboard.permissions.create',
        'store'   => 'dashboard.permissions.store',
        'show'    => 'dashboard.permissions.show',
        'edit'    => 'dashboard.permissions.edit',
        'update'  => 'dashboard.permissions.update',
        'destroy' => 'dashboard.permissions.destroy'
    ]);

    Route::resource('modules', ModuleController::class)->names([
        'index'   => 'dashboard.modules.index',
        'create'  => 'dashboard.modules.create',
        'store'   => 'dashboard.modules.store',
        'show'    => 'dashboard.modules.show',
        'edit'    => 'dashboard.modules.edit',
        'update'  => 'dashboard.modules.update',
        'destroy' => 'dashboard.modules.destroy'
    ]);

    Route::get('usermodules/user/{userId}/submodules', [UserModulePermissionController::class, 'getUserSubmodules'])
    ->name('dashboard.usermodules.getUserSubmodules');

    Route::resource('usermodules', UserModulePermissionController::class)->names([
        'index'   => 'dashboard.usermodules.index',
        'create'  => 'dashboard.usermodules.create',
        'store'   => 'dashboard.usermodules.store',
        'show'    => 'dashboard.usermodules.show',
        'edit'    => 'dashboard.usermodules.edit',
        'update'  => 'dashboard.usermodules.update',
        'destroy' => 'dashboard.usermodules.destroy'
    ]);

    // **Rutas personalizadas para asignar, obtener y revocar permisos**
    Route::post('usermodules/sync-all', [UserModulePermissionController::class, 'syncAll'])->name('dashboard.usermodules.syncAll');
    Route::post('usermodules/sync-modules', [UserModulePermissionController::class, 'syncModules'])->name('dashboard.usermodules.syncModules');
    Route::post('usermodules/sync', [UserModulePermissionController::class, 'syncPermissions'])->name('dashboard.usermodules.sync');
    Route::post('usermodules/assign', [UserModulePermissionController::class, 'assignPermission'])->name('dashboard.usermodules.assign');
    Route::get('usermodules/user/{userId}/permissions', [UserModulePermissionController::class, 'getUserPermissions'])->name('dashboard.usermodules.getUserPermissions');
    Route::post('usermodules/revoke', [UserModulePermissionController::class, 'revokePermission'])->name('dashboard.usermodules.revoke');

    Route::get('modules/{module_id}/children', [UserController::class, 'getChildrenByModule'])
    ->name('dashboard.submodules.byModule');

    // Listar solo las imágenes activas (para la página pública)
    Route::get('slider/images/active', [SliderImageController::class, 'getActive'])
    ->name('dashboard.sliderimages.active');

    // Cambiar el estado (activar/desactivar)
    Route::patch('slider/images/{id}/toggle-status', [SliderImageController::class, 'toggleStatus'])
    ->name('dashboard.sliderimages.toggleStatus');

    // Actualizar el orden de una imagen
    Route::patch('slider/images/{id}/order', [SliderImageController::class, 'updateOrder'])
    ->name('dashboard.sliderimages.updateOrder');
    Route::patch('slider/images/{id}/toggle-status', [SliderImageController::class, 'toggleStatus'])
    ->name('dashboard.sliderimages.toggleStatus');
    Route::resource('slider/images', SliderImageController::class)->names([
        'index'   => 'dashboard.sliderimages.index',
        'create'  => 'dashboard.sliderimages.create',
        'store'   => 'dashboard.sliderimages.store',
        'show'    => 'dashboard.sliderimages.show',
        'edit'    => 'dashboard.sliderimages.edit',
        'update'  => 'dashboard.sliderimages.update',
        'destroy' => 'dashboard.sliderimages.destroy'
    ]);

    Route::resource('publication', PublicationController::class)->names([
        'index'   => 'dashboard.publication.index',
        'create'  => 'dashboard.publication.create',
        'store'   => 'dashboard.publication.store',
        'show'    => 'dashboard.publication.show',
        'edit'    => 'dashboard.publication.edit',
        'update'  => 'dashboard.publication.update',
        'destroy' => 'dashboard.publication.destroy'
    ]);

    Route::resource('report', ReportController::class)->names([
        'index'   => 'dashboard.report.index',
        'create'  => 'dashboard.report.create',
        'store'   => 'dashboard.report.store',
        'show'    => 'dashboard.report.show',
        'edit'    => 'dashboard.report.edit',
        'update'  => 'dashboard.report.update',
        'destroy' => 'dashboard.report.destroy'
    ]);

    Route::resource('association', AssociationController::class)->names([
        'index'   => 'dashboard.association.index',
        'create'  => 'dashboard.association.create',
        'store'   => 'dashboard.association.store',
        'show'    => 'dashboard.association.show',
        'edit'    => 'dashboard.association.edit',
        'update'  => 'dashboard.association.update',
        'destroy' => 'dashboard.association.destroy'
    ]);

    Route::resource('contracs', ContracController::class)->names([
        'index'   => 'dashboard.contracs.index',
        'create'  => 'dashboard.contracs.create',
        'store'   => 'dashboard.contracs.store',
        'show'    => 'dashboard.contracs.show',
        'edit'    => 'dashboard.contracs.edit',
        'update'  => 'dashboard.contracs.update',
        'destroy' => 'dashboard.contracs.destroy'
    ]);

    Route::resource('entities', EntityController::class)->names([
        'index'   => 'dashboard.entities.index',
        'create'  => 'dashboard.entities.create',
        'store'   => 'dashboard.entities.store',
        'show'    => 'dashboard.entities.show',
        'edit'    => 'dashboard.entities.edit',
        'update'  => 'dashboard.entities.update',
        'destroy' => 'dashboard.entities.destroy'
    ]);

    Route::post('/contractors/import', [ContractorController::class, 'import'])->name('dashboard.contractors.import');
    Route::get('/contractors/export', [ContractorController::class, 'exportContractors'])->name('dashboard.contractors.export');
    Route::resource('contractors', ContractorController::class)->names([
        'index'   => 'dashboard.contractors.index',
        'create'  => 'dashboard.contractors.create',
        'store'   => 'dashboard.contractors.store',
        'show'    => 'dashboard.contractors.show',
        'edit'    => 'dashboard.contractors.edit',
        'update'  => 'dashboard.contractors.update',
        'destroy' => 'dashboard.contractors.destroy'
    ]);

    Route::get('/plantofficials/export', [PlantOfficialController::class, 'exportPlantOfficial'])->name('dashboard.plantofficials.export');
    Route::resource('plantofficials', PlantOfficialController::class)->names([
        'index'   => 'dashboard.plantofficials.index',
        'create'  => 'dashboard.plantofficials.create',
        'store'   => 'dashboard.plantofficials.store',
        'show'    => 'dashboard.plantofficials.show',
        'edit'    => 'dashboard.plantofficials.edit',
        'update'  => 'dashboard.plantofficials.update',
        'destroy' => 'dashboard.plantofficials.destroy'
    ]);

    Route::resource('events', EventController::class)->names([
        'index'   => 'dashboard.events.index',
        'create'  => 'dashboard.events.create',
        'store'   => 'dashboard.events.store',
        'show'    => 'dashboard.events.show',
        'edit'    => 'dashboard.events.edit',
        'update'  => 'dashboard.events.update',
        'destroy' => 'dashboard.events.destroy'
    ]);

    Route::resource('settings', EntitySettingController::class)->names([
        'index'   => 'dashboard.settings.index',
        'create'  => 'dashboard.settings.create',
        'store'   => 'dashboard.settings.store',
        'show'    => 'dashboard.settings.show',
        'edit'    => 'dashboard.settings.edit',
        'update'  => 'dashboard.settings.update',
        'destroy' => 'dashboard.settings.destroy'
    ]);

    Route::resource('laws', LawController::class)->names([
        'index'   => 'dashboard.laws.index',
        'create'  => 'dashboard.laws.create',
        'store'   => 'dashboard.laws.store',
        'show'    => 'dashboard.laws.show',
        'edit'    => 'dashboard.laws.edit',
        'update'  => 'dashboard.laws.update',
        'destroy' => 'dashboard.laws.destroy'
    ]);

    Route::post('/mipg/rename/{id}', [MipgController::class, 'rename'])->name('mipg.rename');
    Route::post('/mipg/folder', [MipgController::class, 'storeFolder'])->name('storeFolder');
    Route::post('/mipg/upload', [MipgController::class, 'upload'])->name('mipg.upload');
    Route::post('/mipg/{id}/assign-area', [MipgController::class, 'assignArea']);
    Route::get('/mipg/dependency-summary', [MipgController::class, 'dependencySummary']);
    Route::get('/mipg/type-summary', [MipgController::class, 'typeSummary']);
    Route::patch('/mipg/{id}/toggle-visibility', [MipgController::class, 'toggleVisibility'])->name('mipg.toggle-visibility');
    Route::resource('mipg', MipgController::class)->names([
        'index'   => 'dashboard.mipg.index',
        'create'  => 'dashboard.mipg.create',
        'store'   => 'dashboard.mipg.store',
        'show'    => 'dashboard.mipg.show',
        'edit'    => 'dashboard.mipg.edit',
        'update'  => 'dashboard.mipg.update',
        'destroy' => 'dashboard.mipg.destroy'
    ]);

    Route::post('/institutionalcontent/upload-image', [InstitutionalContentController::class, 'uploadImage'])->name('dashboard.institutionalcontent.upload-image');
    Route::resource('institutionalcontent', InstitutionalContentController::class)->names([
        'index'   => 'dashboard.institutionalcontent.index',
        'create'  => 'dashboard.institutionalcontent.create',
        'store'   => 'dashboard.institutionalcontent.store',
        'show'    => 'dashboard.institutionalcontent.show',
        'edit'    => 'dashboard.institutionalcontent.edit',
        'update'  => 'dashboard.institutionalcontent.update',
        'destroy' => 'dashboard.institutionalcontent.destroy'
    ]);

    Route::get('/service/type', [ServiceTypeController::class, 'indexData']);
    Route::resource('servicetype', ServiceTypeController::class)->names([
        'index'   => 'dashboard.servicetype.index',
        'create'  => 'dashboard.servicetype.create',
        'store'   => 'dashboard.servicetype.store',
        'show'    => 'dashboard.servicetype.show',
        'edit'    => 'dashboard.servicetype.edit',
        'update'  => 'dashboard.servicetype.update',
        'destroy' => 'dashboard.servicetype.destroy'
    ]);
    Route::resource('entityservice', EntityServiceController::class)->names([
        'index'   => 'dashboard.entityservice.index',
        'create'  => 'dashboard.entityservice.create',
        'store'   => 'dashboard.entityservice.store',
        'show'    => 'dashboard.entityservice.show',
        'edit'    => 'dashboard.entityservice.edit',
        'update'  => 'dashboard.entityservice.update',
        'destroy' => 'dashboard.entityservice.destroy'
    ]);

    Route::get('contentpages/options', [ContentPageController::class, 'pagesForSelect'])->name('dashboard.contentpages.options');
    Route::resource('contentpages', ContentPageController::class)->names([
        'index'   => 'dashboard.contentpages.index',
        'create'  => 'dashboard.contentpages.create',
        'store'   => 'dashboard.contentpages.store',
        'show'    => 'dashboard.contentpages.show',
        'edit'    => 'dashboard.contentpages.edit',
        'update'  => 'dashboard.contentpages.update',
        'destroy' => 'dashboard.contentpages.destroy'
    ]);

    Route::resource('contentitems', ContentItemController::class)->names([
        'index'   => 'dashboard.contentitems.index',
        'create'  => 'dashboard.contentitems.create',
        'store'   => 'dashboard.contentitems.store',
        'show'    => 'dashboard.contentitems.show',
        'edit'    => 'dashboard.contentitems.edit',
        'update'  => 'dashboard.contentitems.update',
        'destroy' => 'dashboard.contentitems.destroy'
    ]);

    // CONSECUTIVES
    Route::prefix('consecutives')->group(function () {
        Route::resource('series', SeriesController::class)->names('dashboard.series'); 
        Route::get('/', [ConsecutiveController::class, 'index'])->name('dashboard.consecutives.index');
        Route::get('/generar', [ConsecutiveController::class, 'create'])->name('dashboard.consecutives.create');
        Route::post('/generar', [ConsecutiveController::class, 'store'])->name('dashboard.consecutives.store');
        Route::get('/{consecutive}/edit', [ConsecutiveController::class, 'edit'])->name('dashboard.consecutives.edit');
        Route::put('/{consecutive}', [ConsecutiveController::class, 'update'])->name('dashboard.consecutives.update');
        Route::get('/{consecutive}', [ConsecutiveController::class, 'show'])->name('dashboard.consecutives.show');
        Route::patch('/{consecutive}/anular', [ConsecutiveController::class, 'cancel'])->name('dashboard.consecutives.cancel');
    });

    // PERSONS
    Route::prefix('persons')->group(function () {
        Route::get('/search', [PersonController::class, 'search'])->name('persons.search');
        Route::post('/store', [PersonController::class, 'store'])->name('persons.store');
    });
});