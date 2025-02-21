<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\User;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vehicle')]
class VehicleRegistrationController extends AbstractController
{
    #[Route('/', name: 'vehicle_index', methods: ['GET'])]
    public function index(VehicleRepository $vehicleRepository): JsonResponse
    {
        $vehicles = $vehicleRepository->findAll();
        return $this->json($vehicles);
    }

    #[Route('/create', name: 'vehicle_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $vehicle = new Vehicle();
        $vehicle->setBrand($data['brand']);
        $vehicle->setModel($data['model']);
        $vehicle->setColor($data['color']);
        $vehicle->setLicensePlate($data['licensePlate']);
        $vehicle->setSeatsCount($data['seatsCount']);

        $owner = $entityManager->getRepository(User::class)->find($data['ownerId']);
        if (!$owner) {
            return $this->json(['error' => 'Owner not found'], 404);
        }

        $vehicle->setOwner($owner);

        $entityManager->persist($vehicle);
        $entityManager->flush();

        return $this->json($vehicle, 201);
    }

    #[Route('/{id}', name: 'vehicle_show', methods: ['GET'])]
    public function show(Vehicle $vehicle): JsonResponse
    {
        return $this->json($vehicle);
    }

    #[Route('/{id}/edit', name: 'vehicle_edit', methods: ['PUT'])]
    public function edit(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $vehicle->setBrand($data['brand']);
        $vehicle->setModel($data['model']);
        $vehicle->setColor($data['color']);
        $vehicle->setLicensePlate($data['licensePlate']);
        $vehicle->setSeatsCount($data['seatsCount']);

        $entityManager->flush();

        return $this->json($vehicle);
    }

    #[Route('/{id}', name: 'vehicle_delete', methods: ['DELETE'])]
    public function delete(Vehicle $vehicle, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($vehicle);
        $entityManager->flush();

        return $this->json(null, 204);
    }
}
