<?php

namespace Ekyna\Bundle\CmsBundle\Controller\Editor;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class RowController
 * @package Ekyna\Bundle\CmsBundle\Controller\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class RowController extends BaseController
{
    public function createBlockAction(Request $request)
    {
        $this->checkAuthorization();

        $row = $this->findRow(intval($request->attributes->get('rowId')));

        // TODO should be handled by validation
        if (6 <= $row->getBlocks()->count()) {
            throw new BadRequestHttpException('Row max block count reached.');
        }

        $this->getEditor()
            ->setEnabled(true)
            ->createDefaultBlock(null, [], $row);

        $errorList = $this->validate($row);
        if (0 < $errorList->count()) {
            throw new \Exception('Invalid row');
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($row);
        $manager->flush();

        $data = ['rows' => [$this->getViewBuilder()->buildRow($row)]];

        return $this->buildResponse($data);
    }
}
