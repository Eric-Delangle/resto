<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Stripe\StripeService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    /**
     * Methode de paiement avec stripe
     * @Route("/payment/{id}", name="payment_stripe")
     * @IsGranted("ROLE_USER")
     */
    public function paymentStripe($id, PurchaseRepository $purchaseRepo, StripeService $stripeService)
    {

        $purchase = $purchaseRepo->find($id);
        dump($purchase);
        $intent = $stripeService->getPaymentIntent($purchase);


        if (!$purchase) {
            return $this->redirectToRoute('member_account');
        }

        return $this->render('purchase/payment.html.twig', [
            'id' => $id,
            'purchase' => $purchase,
            'clientSecret' => $intent->client_secret,
            'stripePublicKey' => $stripeService->getPublicKey()
        ]);
    }

    /**
     * Methode appelée en cas de succes du paiement
     * @Route("/payment/success/{id}", name="payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepo, EntityManagerInterface $em)
    {
        $purchase = $purchaseRepo->find($id);

        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "La commande n'existe pas.");
            return $this->redirectToRoute("member_account");
        }
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();

        $this->addFlash('success', 'Votre commande est lancée !');
        return $this->redirectToRoute("member_account");
    }
}
