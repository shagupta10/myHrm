<?php

/**
 * TrainingTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class TrainingTable extends PluginTrainingTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object TrainingTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Training');
    }
}