<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class ApiController extends AbstractController
{
    /**
     * Create a form and submit the incoming request data.
     *
     * @param string $formType
     * @param mixed $entity The entity to use.
     * @param Request $request
     *
     * @return FormInterface
     */
    protected function getForm(Request $request, string $formType, $entity = null): FormInterface
    {
        return $this->createForm($formType, $entity, [
            'csrf_protection' => false,
        ])->submit($request->request->all());
    }

    /**
     * Return array of form errors.
     *
     * @param Form $form
     *
     * @return array
     */
    protected function getFormErrors(Form $form): array
    {
        $errors = [];

        foreach ($form->getErrors(true, true) as $error) {
            $errors[$error->getOrigin()->getName()][] = $error->getMessage();
        }

        return $errors;
    }
}
