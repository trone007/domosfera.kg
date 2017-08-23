<?php
/**
 * Created by PhpStorm.
 * User: stone
 * Date: 02/05/17
 * Time: 17:55
 */

namespace AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Image as Image;

class UpdateImageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:image-update')
            ->setDescription('Updates image located in tmp folder')
            ->addArgument('image', InputArgument::REQUIRED, 'UUID of the image located in ONE C service')
            ->addArgument('width', InputArgument::REQUIRED, 'width of the image')
            ->addArgument('height', InputArgument::REQUIRED, 'height of the image');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $image = $input->getArgument('image');
        $width = $input->getArgument('width');
        $height = $input->getArgument('height');

        $rootDir = dirname($this->getContainer()->get('kernel')->getRootDir());
        $dir = $rootDir . '/web/tmp/';
        $filename = $dir. $image .$width . 'x' . $height.'.jpg';

        $image = file_get_contents('http://www.gallery.kg/image/'. urlencode('кыргызстан'). '/' . $image. '/'.$width. '/'. $height);

        if(file_exists($filename)) {
//            if(sha1_file($filename) == sha1($image)) {
                $output->write(base64_encode(file_get_contents($filename)));
                return true;
//            }
        }

        $output->write(base64_encode($image));
        $image = imagecreatefromstring($image);
        imagejpeg($image, $filename, 50);
        imagedestroy($image);
//
//         outputs a message followed by a "\n"
//
//         outputs a message without adding a "\n" at the end of the line
//        $output->writeln('file' . $filename . 'is saved');
//        $output->write('create a user.');
    }

}