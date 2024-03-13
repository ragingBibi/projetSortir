<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPseudo($user->getFirstName().'.'.$user->getLastName());

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,

                    $form->get('plainPassword')->getData()
                )
            );

            //pour passer le admin
            /*$user->setIsAdmin(false);
        $user->setIsActive(true);*/
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@sortir.com', 'Mail Verifier'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre adresse email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            //TODO : Créer un sender pour envoyer un email à l'utilisateur nouvellement créé
            // do anything else you need here, like send an email
//           $text = 'Bonjour ' . $user->getFirstName() . ' ! Bienvenue sur Jet:Lagged:Brains. Pour vous connecter sur Jet:Lagged:Brains,
//           veuillez saisir votre adresse Email et comme mot de passe la premiere lettre de votre prenom attaché à votre nom. A votre première connexion,
//             veuillez modifier votre mot de passe et votre pseudo.';
//            $sender->sendEmail('Nouvelle inscription sur Amazooz', $text, 'admin@amazooz.com');
//            $sender->sendEmail('Bienvenue sur Amazooz', 'Voila, voila...', $user->getEmail());

            return $security->login($user, AppAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }


        $this->addFlash('success', 'Votre adresse email a été validée!');

        return $this->redirectToRoute('app_register');
    }
}
