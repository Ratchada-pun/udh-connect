<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TblPatient]].
 *
 * @see TblPatient
 */
class TblPatientQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblPatient[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblPatient|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
