<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[TblAppointment]].
 *
 * @see TblAppointment
 */
class TblAppointmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TblAppointment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TblAppointment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
