<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminUtilityController extends AbstractController
{
    /**
     * @Route("/admin/utility/users", methods="GET", name="admin_utility_users")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function getUsersApi(UserRepository $userRepository, Request $request) // The job of this endpoint is to return an array of User objects as JSON
    {
        $users = $userRepository->findAllMatching($request->query->get('query'));

        return $this->json([ // returning the user objects which are fetched by this class and putting them into an array under a 'users' key
            'users' => $users
        ], 200, [], ['groups' => ['main']]); // Passing it a group called main, which is defined in the User class as annotations.
                                                    // This makes the serializer only serialize the properties in this group
    }

}