<?php

/**
 * GroupFieldTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class GroupFieldTable extends PluginGroupFieldTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object GroupFieldTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('GroupField');
    }
}