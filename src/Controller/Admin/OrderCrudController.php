<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use function Sodium\add;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator;

    /**
     * OrderCrudController constructor.
     * @param EntityManagerInterface $entityManager
     * @param AdminUrlGenerator $adminUrlGenerator
     */
    public function __construct( EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }


    public function configureActions(Actions $actions): Actions
    {
        $updateValidated = Action::new('updateValidated', 'Livrée', 'fas fa-check' )->linkToCrudAction('updateValidated');
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open' )->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck' )->linkToCrudAction('updateDelivery');


        return $actions
            ->add('index', 'detail')
            ->add('detail', $updateValidated)
            ->add('detail', $updateDelivery)
            ->add('detail', $updatePreparation);
    }

    public function updatePreparation(AdminContext $context): RedirectResponse
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:green;'><strong>La commande " . $order->getReference() . " est bien <u>en cours de préparation</u></strong></span>");

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        //TODO -> Mail commande préparée

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context): RedirectResponse
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(1);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:orange;'><strong>La commande " . $order->getReference() . " est bien <u>en cours de livraison</u></strong></span>");

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        //TODO -> Mail commande envoyée

        return $this->redirect($url);
    }

    public function updateValidated(AdminContext $context): RedirectResponse
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(0);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color:blue;'><strong>La commande " . $order->getReference() . " est bien <u>effectuée</u></strong></span>");

        $url = $this->adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction(Action::INDEX)
            ->generateUrl();

        //TODO -> Mail commande envoyée

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['state' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id', 'Identifiant'),
            DateField::new('createdAt', 'Passée le'),
            TextField::new('user.fullName', 'Nom'),
            TextField::new('delivery', 'Adresse de livraison')->onlyOnDetail()->renderAsHtml(),
            MoneyField::new('total')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new( 'carrierPrice','frais de port')->setCurrency('EUR'),
            ChoiceField::new('state', 'Etat')->setChoices([
                '❌ Non payée' => 4,
                '🔔 Payée' => 0,
                '📦 Préparation en cours' => 1,
                '🚛 Livraison en cours' => 2,
                '✔ Livrée ️' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }

}
