<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        // Handle file upload
            /** @var UploadedFile $profilePictureFile */
            $profilePictureFile = $form->get('profilepicture')->getData();

            if ($profilePictureFile) {
                // Generate a unique name for the file
                $newFilename = uniqid().'.'.$profilePictureFile->guessExtension();

                // Move the file to the uploads directory
                $profilePictureFile->move(
                    $this->getParameter('images_directory'), // Defined in services.yaml
                    $newFilename
                );

                // Update the profilepicture field in the User entity
                $user->setProfilepicture($newFilename);
            }

            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // Send welcome email
            $email = (new Email())
                ->from('riadhtr21@gmail.com') // Replace with your sender email
                ->to($user->getEmail()) // Use the email from the form
                ->subject('Merci de vous être inscrit chez nous. Bienvenue !')
                ->text('Chère Famille Cultify,
Nous sommes ravis de vous accueillir dans notre communauté ! Votre voyage avec nous ne fait que commencer, et nous avons hâte de voir comment vous allez grandir et vous épanouir. Ensemble, créons quelque chose d’extraordinaire. Bienvenue dans la famille !');

            $mailer->send($email);

            // Log in the user automatically
            return $security->login($user, UserAuthenticator::class, 'main');
        }

        return $this->render('registration/auth.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}