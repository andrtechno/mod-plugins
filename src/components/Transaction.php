<?php

namespace panix\mod\plugins\components;

use Yii;

/**
 * Class Transaction
 * @package panix\mod\plugins\components
 * @author Lukyanov Andrey <loveorigami@mail.ru>
 */
class Transaction
{
    /**
     * @return \yii\db\Transaction
     */
    public function begin()
    {
        return Yii::$app->db->beginTransaction();
    }

    /**
     * @param \yii\db\Transaction $transaction
     */
    public function commit($transaction)
    {
        $transaction->commit();
    }

    /**
     * @param \yii\db\Transaction $transaction
     */
    public function rollBack($transaction)
    {
        $transaction->rollBack();
    }
}