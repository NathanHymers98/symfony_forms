<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('Will be intercepted before getting here');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $formAuthenticator)
    {
        $form = $this->createForm(UserRegistrationFormType::class); // Not passing a second argument because I want it to create a new user object

        $form->handleRequest($request); // Checks to see if the request is POST or GET
                                        // If GET the first method will be false, so it will go down to the return to return the form to the twig view
                                        // If POST the first method will be true and depending if it is valid or not the second method could be true
                                        // When both are true, it gets the data that was entered in the form, then encodes the password
                                        // After the data has been received from the form and the password is encoded, it saves them both to the database

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var UserRegistrationFormModel $user */
            $userModel = $form->getData();

            $user = new User();
            $user->setEmail($userModel->email); // Since the form will now create a UserRegistraionFormModel object instead of a User object
                                                // we need to point to where the users email is when they create an account, which will be in the $userModel's email property now
            $user->setPassword($passwordEncoder->encodePassword( // Uses the $user and $form['plainPassword'] to encode the password and set the encoded password to the $user that is passed in the database
                $user,
                $userModel->plainPassword // again, pointing to where the password is sent to when the user registers an account, which is on the $userModel's plainPassword property
            ));

            if (true === $userModel->agreeTerms) { // If the agreeTerms field data received from the form is exactly equal to true
                $user->agreeTerms(); // Then call this method which set the Users agreedTermsAt property with a DateTime object
            }

            $em = $this->getDoctrine()->getManager(); // Saves the user and their encoded password
            $em->persist($user);
            $em->flush();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $formAuthenticator,
                'main'
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }
}
