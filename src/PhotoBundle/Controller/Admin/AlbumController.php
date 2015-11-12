<?php

namespace PhotoBundle\Controller\Admin;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use PhotoBundle\Entity\Album;
use PhotoBundle\Form\AlbumType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Album controller.
 *
 */
class AlbumController extends Controller
{

    /**
     * Отображение списка альбомов
     *
     * @return Response
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $albums = $em->getRepository('PhotoBundle:Album')->findAll();

        return $this->render('@Photo/Pages/Admin/Album/index.html.twig', array(
            'albums' => $albums,
        ));
    }

    /**
     * Включение отображения альбома на сайте
     *
     * @param $albumId integer Идентификатор альбома
     *
     * @return JsonResponse
     */
    public function enabledAction($albumId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Album $album */
        $album = $em->getRepository('PhotoBundle:Album')
            ->find($albumId);
        if (!$album) {
            return new JsonResponse(array('success' => false));
        }
        $album->setEnabled(true);

        $em->persist($album);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }

    /**
     * Выключение отображения альбома на сайте
     *
     * @param $albumId integer Идентификатор альбома
     *
     * @return JsonResponse
     */
    public function disabledAction($albumId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Album $album */
        $album = $em->getRepository('PhotoBundle:Album')
            ->find($albumId);
        if (!$album) {
            return new JsonResponse(array('success' => false));
        }
        $album->setEnabled(false);

        $em->persist($album);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }

    /**
     * Удаление альбома
     *
     * @param $albumId integer Идентификатор альбома
     *
     * @return JsonResponse
     */
    public function deleteAction($albumId)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Album $album */
        $album = $em->getRepository('PhotoBundle:Album')
            ->find($albumId);
        if (!$album) {
            return new JsonResponse(array('success' => false));
        }

        $em->remove($album);
        $em->flush();

        return new JsonResponse(array('success' => true));
    }

    /**
     * Создание альбома
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $entity = new Album();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_album_show', array('albumId' => $entity->getId())));
        }

        return $this->render('@Photo/Pages/Admin/Album/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Создание формы для создания альбома
     *
     * @param Album $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Album $entity)
    {
        $form = $this->createForm(new AlbumType(), $entity, array(
            'action' => $this->generateUrl('admin_album_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Создать'));

        return $form;
    }

    /**
     * Отображение формы для создания нового альбома
     *
     * @return Response
     */
    public function newAction()
    {
        $entity = new Album();
        $form   = $this->createCreateForm($entity);

        return $this->render('@Photo/Pages/Admin/Album/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Нахождение и отображение сущности альбома
     *
     * @param $albumId
     *
     * @return Response
     */
    public function showAction($albumId)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PhotoBundle:Album')->find($albumId);

        if (!$entity) {
            throw $this->createNotFoundException('Не удалось найти альбом.');
        }

        return $this->render('@Photo/Pages/Admin/Album/show.html.twig', array('entity' => $entity));
    }

    /**
     * Отображение формы для редактирования альбома
     * @param $albumId
     *
     * @return Response
     */
    public function editAction($albumId)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PhotoBundle:Album')->find($albumId);

        if (!$entity) {
            throw $this->createNotFoundException('Не удалось найти альбом.');
        }

        $editForm = $this->createEditForm($entity);

        return $this->render('@Photo/Pages/Admin/Album/edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        ));
    }

    /**
    * Создание формы для редактирования альбома
     *
    * @param Album $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Album $entity)
    {
        $form = $this->createForm(new AlbumType(), $entity, array(
            'action' => $this->generateUrl('admin_album_update', array('albumId' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Обновить'));

        return $form;
    }

    /**
     * Обновление соществующего альбома
     *
     * @param Request $request
     * @param $albumId
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request, $albumId)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('PhotoBundle:Album')->find($albumId);

        if (!$entity) {
            throw $this->createNotFoundException('Не удалось найти альбом.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('admin_album_edit', array('albumId' => $albumId)));
        }

        return $this->render('@Photo/Pages/Admin/Album/edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        ));
    }
}
