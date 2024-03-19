<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\CSVFileImportType;
use App\Form\RegistrationFormType;
use App\Repository\CampusRepository;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
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

            $user->setPseudo($user->getFirstName() . '.' . $user->getLastName());

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

    #[Route('/importCSV', name: 'app_importCSV')]
    public function importCSV(CampusRepository $campusRepository, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        $form = $this->createForm(CSVFileImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('csv_file')->getData();


            if ($file) {
                $fileName = 'userCSVImport' . uniqid() . '.csv';
                $newFilePath = $file->move('uploads/csv', $fileName);

                $csv = Reader::createFromPath($newFilePath)->setHeaderOffset(0);
                $records = $csv->getRecords();

                foreach ($records as $record) {

                    $user = new User();
                    $campus = $campusRepository->findCampusbyName($record['campus']);
                    $user->setCampus($campus);
                    $user->setFirstName($record['firstName']);
                    $user->setLastName($record['lastName']);
                    $user->setPseudo($user->getFirstName() . '.' . $user->getLastName());
                    $user->setEmail($record['email']);
                    $user->setRoles(['ROLE_USER']);
                    $user->setPassword('$2y$13$9Q7KWgtUtGeeIIJWDz23NeyMF/oLrzlwTsElSjYfC37kCXvz.xav6');
                    $user->setPhoneNumber($record['phoneNumber']);
                    $user->setIsAdmin(0);
                    $user->setIsActive(1);
                    $user->setIsVerified(1);


                    $entityManager->persist($user);


                }
                $entityManager->flush();
                $this->addFlash('success', 'Les données du fichier CSV ont été importées avec succès dans la base de données.');
                return $this->redirectToRoute('app_importCSV');
            } else {
                // Handle the case where no file was uploaded (e.g., display an error message)
                $this->addFlash('error', 'Aucun fichier CSV n\'a été sélectionné.');
            }
        }


        return $this->render('user/import_csv_file_form.html.twig', [
            'CSVImportForm' => $form,
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
