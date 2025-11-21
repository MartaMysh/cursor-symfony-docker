<?php

namespace App\Command;

use AllowDynamicProperties;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AllowDynamicProperties] #[AsCommand(
    name: 'app:fill-users',
    description: 'Importujemy informację o produktach z pliku XLSX',
)]
class FillUsersCommand extends Command
{
    public function __construct(
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Importuje użytkowników z pliku XLSX')
            ->addArgument('file', InputArgument::REQUIRED, 'Ścieżka do pliku XLSX');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $filePath = $input->getArgument('file');

        if ($filePath) {
            $io->note(sprintf('You passed an argument: %s', $filePath));
        }

        if (!file_exists($filePath)) {
            $io->note('Plik nie istnieje!');
            return Command::FAILURE;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Pomijamy nagłówki
        foreach (array_slice($rows, 1) as $row) {

            $id = $row[0];
            $firstname = $row[1];
            $lastname  = $row[2];
            $login     = $row[3];
            $password  = $row[4];

            // Sprawdzamy czy użytkownik istnieje
            $existing = $this->em->getRepository(User::class)->findOneBy([
                'login' => $login
            ]);

            if ($existing) {
                $io->note(sprintf('Pomijam, użytkownik już istnieje: %s', $login));
                continue;
            }

            if (empty($firstname) || empty($lastname) || empty($login) || empty($password)) {
                $io->note(sprintf('Pomijam, brakuje wymmaganej informacji dla utworzenia użytkownika. Id - %s', $id));
                continue;
            }

            $user = new User();
            $user->setFirstName($firstname);
            $user->setLastName($lastname);
            $user->setLogin($login);
            $user->setRoles(['ROLE_USER']);

            // Hashowanie hasła
            $hashed = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashed);

            $this->em->persist($user);
        }

        $this->em->flush();

        $io->note('Import użytkowników zakończony pomyślnie.');
        return Command::SUCCESS;
    }
}
