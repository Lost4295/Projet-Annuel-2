<?php

namespace App\Controller\Admin;

use App\Entity\Api;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use App\Entity\User;
use App\Entity\Email;
use App\Entity\Appartement;
use App\Entity\Commentaire;
use App\Entity\Fichier;
use App\Entity\Location;
use App\Entity\Professionnel;
use App\Entity\Service;
use App\Entity\Ticket;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminDashboardController extends AbstractDashboardController
{
    public function __construct(
        private ChartBuilderInterface $chartBuilder,
    ) {
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'Views',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [12, 29, 3, 5, 2, 3, 7],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ]);


        return $this->render('admin/dashboard.html.twig', [
            'chart' => $chart,
        ]);
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            // the name visible to end users
            // ->setTitle('ACME Corp.')
            // you can include HTML contents too (e.g. to link to an image)
            ->setTitle('Paris Caretaker Services <small>Admin Dashboard</small>')

            // by default EasyAdmin displays a black square as its default favicon;
            // use this method to display a custom favicon: the given path is passed
            // "as is" to the Twig asset() function:
            // <link rel="shortcut icon" href="{{ asset('...') }}">
            ->setFaviconPath('brand/bootstrap-logo.svg')

            // the domain used by default is 'messages'
            // ->setTranslationDomain('my-custom-domain')

            // there's no need to define the "text direction" explicitly because
            // its default value is inferred dynamically from the user locale
            // ->setTextDirection('ltr')

            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
            // ->renderSidebarMinimized()

            // by default, users can select between a "light" and "dark" mode for the
            // backend interface. Call this method if you prefer to disable the "dark"
            // mode for any reason (e.g. if your interface customizations are not ready for it)
            // ->disableDarkMode()

            // by default, all backend URLs are generated as absolute URLs. If you
            // need to generate relative URLs instead, call this method
            
            ->generateRelativeUrls()

            // set this option if you want to enable locale switching in dashboard.
            // IMPORTANT: this feature won't work unless you add the {_locale}
            // parameter in the admin dashboard URL (e.g. '/admin/{_locale}').
            // the name of each locale will be rendered in that locale
            // (in the following example you'll see: "English", "Polski")
            ->setLocales(['en', 'fr'])
            // to customize the labels of locales, pass a key => value array
            // (e.g. to display flags; although it's not a recommended practice,
            // because many languages/locales are not associated to a single country)
            ->setLocales([
                'en' => '🇬🇧 English',
                'fr'=>  '🇫🇷 Français'
            ])
            // to further customize the locale option, pass an instance of
            // EasyCorp\Bundle\EasyAdminBundle\Config\Locale
            // ->setLocales([
            //     'en', // locale without custom options
            //     Locale::new('pl', 'polski', 'translate'), // custom label and icon
            //     Locale::new('fr', 'français', 'translate') // custom label and icon
            // ])
        ;
    }
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getFullName())
            // use this method if you don't want to display the name of the user
            // ->displayUserName(false)

            // you can return an URL with the avatar image
            // ->setAvatarUrl('https://...')
            // ->setAvatarUrl($user->getProfileImageUrl())
            // use this method if you don't want to display the user image
            ->displayUserAvatar(false)
            // you can also pass an email address to use gravatar's service
            // ->setGravatarEmail($user->getMainEmailAddress())

            // you can use any type of menu item, except submenus
            ->setMenuItems([
                MenuItem::linkToRoute('myprofile', 'fa fa-id-card', '...', ['...' => '...']),
                MenuItem::linkToRoute('settings', 'fa fa-user-cog', '...', ['...' => '...']),
                MenuItem::section(),
                MenuItem::linkToLogout('logout', 'fa fa-sign-out'),
            ]);
    }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl("homepage","fa fa-home" , "/" );
        yield MenuItem::linkToDashboard('dashboard', 'fa fa-home');
        yield MenuItem::section('adminside');
        yield MenuItem::linktoRoute('sendmail', 'fa fa-share-square', 'make_email');
        yield MenuItem::linktoRoute('apistate', 'fa fa-robot', 'api_state');
        yield MenuItem::linkToCrud('apis', 'fa fa-robot', Api::class);
        yield MenuItem::linktoRoute('tarifs', 'fa fa-shopping-cart', 'tarifs');
        yield MenuItem::linktoRoute('transaction', 'fa fa-list', 'all_states');
        yield MenuItem::linkToCrud('ticket', 'fa fa-plus', Ticket::class);
        // yield MenuItem::linkToCrud('Blog Posts', 'fa fa-file-text', BlogPost::class);
        yield MenuItem::section('userrl');
        // yield MenuItem::linkToCrud('Comments', 'fa fa-comment', Comment::class);
        yield MenuItem::linkToCrud('users', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('pro', 'fa fa-id-card', Professionnel::class);
        yield MenuItem::section('obrl');
        yield MenuItem::linkToCrud('commentaire', 'fa fa-comment', Commentaire::class);
        yield MenuItem::linkToCrud('email', 'fa fa-envelope', Email::class);
        yield MenuItem::linkToCrud('file', 'fa fa-files-o ', Fichier::class);
        yield MenuItem::linkToCrud('service', 'fa fa-tasks', Service::class);
        yield MenuItem::section('obrl');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        yield MenuItem::linkToCrud('appart', 'fa fa-building', Appartement::class);
        yield MenuItem::linkToCrud('loca', 'fa fa-plus', Location::class);
        // yield MenuItem::linkToCrud('appart', 'fa fa-building', Appartement::class);
        yield MenuItem::linktoRoute('crappart', 'fa fa-chart-bar', 'appartement_create');
        yield MenuItem::linktoRoute('croplus', 'fa fa-chart-bar', 'create_plus');
    }

}
