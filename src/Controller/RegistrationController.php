<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Repository\UserRepository;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json($form, Response::HTTP_BAD_REQUEST);
        }    
        
        $user->setCreatedAt(new \DateTime());
        $user->setEnabled(true);
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        $entityManager->persist($user);
        $entityManager->flush();

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('libgeo@univali.br', 'LibGeo Mail Bot'))
                ->to($user->getEmail())
                ->subject('Por favor confirme seu email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );

        return new JsonResponse(['success'=>true]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        try {
            $id = $request->get('id');


            if ($id !== null) {
                $user = $userRepository->find($id);

                if ($user !== null) {
                    $this->emailVerifier->handleEmailConfirmation($request, $user);        

                    return $this->redirect($_ENV['FRONTEND_URL'].'/login?confirmation=1');
                }
            }

            return $this->redirect($_ENV['FRONTEND_URL'].'/login?confirmation=2');
        } catch (VerifyEmailExceptionInterface $exception) {
            return $this->redirect($_ENV['FRONTEND_URL'].'/login?confirmation=0');
        }
        
    }
}
