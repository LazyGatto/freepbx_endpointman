<?php
namespace FreePBX\modules\Endpointman;

class Templates
{
    public $epm;
    protected $freepbx;
    protected $db;

    const TABLES = [
        'template_list' => 'endpointman_template_list',
        'mac_list'      => 'endpointman_mac_list',
    ];

    public function __construct($epm) 
    {
        $this->epm      = $epm;
        $this->freepbx  = $epm->freepbx;
        $this->db       = $epm->freepbx->Database;
    }


    /**
     * Get the template settings for a specific template
     * 
     * @param int $template_id The ID of the template
     * @param bool $custom Whether or not this is a custom template
     * @return array The settings for the template
     * 
     */
    public function getConfigGlobal(int $template_id, bool $custom = true)
    {
        if($custom == false)
        {
            //This is a group template
            $sql = sprintf('SELECT global_settings_override FROM %s WHERE id = :id', self::TABLES['template_list']);
        }
        else
        {
            //This is an individual template
            $sql = sprintf('SELECT global_settings_override FROM %s WHERE id = :id', self::TABLES['mac_list']);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $template_id]);

        // Get Only the first column
        $data = $stmt->fetch(\PDO::FETCH_BOTH);
        $data = $data[0] ?? "";

        if (empty($data))
        {
            return [];
        }
        
        // Unserialize the data
        $settings = unserialize(stripslashes($data)) ?? [];
        return $settings;
    }

    public function setConfigGlobal(int $template_id, bool $custom = true, array $settings = [])
    {
        if (empty($settings))
        {
            $settings = null;
        }
        else
        {
            $settings = addslashes(serialize($settings));
        }
        if($custom == false)
        {
            //This is a group template
            $sql = sprintf('UPDATE %s SET global_settings_override = :new_config WHERE id = :id', self::TABLES['template_list']);
        }
        else
        {
            //This is an individual template
            $sql = sprintf('UPDATE %s SET global_settings_override = :new_config WHERE id = :id', self::TABLES['mac_list']);
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'new_config' => $settings,
            'id'         => $template_id,
        ]);
        return true;
    }


}
