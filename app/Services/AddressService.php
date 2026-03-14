<?php

namespace App\Services;

use App\Repositories\AddressRepository;

class AddressService
{
    private AddressRepository $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    public function createAddress(int $entityId, string $cep, string $rua, string $numero, ?string $complemento, string $bairro, string $cidade, string $estado, int $entityType): ?int
    {
        return $this->addressRepository->create($entityId, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $entityType);
    }

    public function updateAddress(int $entityId, string $cep, string $rua, string $numero, ?string $complemento, string $bairro, string $cidade, string $estado, int $entityType): bool
    {
        return $this->addressRepository->update($entityId, $cep, $rua, $numero, $complemento, $bairro, $cidade, $estado, $entityType);
    }

    public function deleteAddressByEntity(int $entityId, int $entityType): bool
    {
        return $this->addressRepository->deleteByEntity($entityId, $entityType);
    }

    public function getAddressByEntity(int $entityId, int $entityType): ?array
    {
        return $this->addressRepository->findByEntity($entityId, $entityType);
    }

    public function getAllAddresses(): array
    {
        return $this->addressRepository->getAllAddresses();
    }
}
