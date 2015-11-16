<?php

namespace PhotoBundle\Controller\Admin;

use PhotoBundle\Entity\Photo;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PhotoController extends Controller
{
    public function indexAction($albumUrl)
    {
        $album = $this->getDoctrine()
            ->getRepository('PhotoBundle:Album')
            ->getAlbumByURL($albumUrl);
        if (!$album) {
            throw $this->createNotFoundException("Не найден альбом по URL: {$albumUrl}");
        }

        return $this->render('PhotoBundle:Pages/Admin/Photo:index.html.twig', array(
            'activeAlbumId' => $album->getId(),
            'photos' => $album->getPhotos()->toArray()
        ));
    }

    public function uploaderAction(Request $request)
    {
        // Получение переданных файлов и информации об альбоме из запроса
        $file = $_FILES['uploadedImages'];
        $tmpFileName = $file['tmp_name'];
        if (is_uploaded_file($tmpFileName)) {
            chmod($tmpFileName, 0660);
        }
        $albumID = $request->get('albumID');

        // Создание экземпляра Imagick и образка изображение до нужного размера
        try {
            $image = new \Imagick($tmpFileName);
        } catch (\ImagickException $e) {
            return new JsonResponse(array('success' => false, 'errorMessage' => ["Файл {$file['tmp_name']} не загрузился"]));
        }
        $image->cropImage(800, 800, 0, 0);

        // Формирование нового имени файла
        $hashFile = md5_file($tmpFileName);
        $newFileName = $hashFile . '.jpg';

        // Сохранение файла в папки
        $uploadDir = $request->server->get('DOCUMENT_ROOT') . '/uploaded/images';
        $imagePaths = array(
            '800x800' => "{$uploadDir}/800x800/",
            '200x200' => "{$uploadDir}/200x200/"
        );
        foreach ($imagePaths as $size => $path) {
            $dimensions = explode('x', $size);
            $image->thumbnailImage((int)$dimensions[0], (int)$dimensions[1], true, false);
            $image->writeimage($path . $newFileName);
        }

        // Сохранение информации о файле в базе
        $em = $this->getDoctrine()->getManager();
        $album = $em->getRepository('PhotoBundle:Album')
            ->find($albumID);
        if (!$album) {
            return new JsonResponse(array('success' => false, 'errorMessage' => ["Переданы не корректные парраметры"]));
        }

        $photo = new Photo();
        $photo->setAlbum($album)
            ->setEnabled(1)
            ->setName($newFileName);
        $em->persist($photo);
        $em->flush();


        $images = array('success' => true, 'fileName' => $newFileName);
        //$images = '';
        return new JsonResponse($images);
    }

    public function deleteAction($photoId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Photo $photo */
        $photo = $em->getRepository('PhotoBundle:Photo')
            ->find($photoId);
        if (!$photo) {
            return new JsonResponse(array('success' => false));
        }

        $em->remove($photo);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }
}
