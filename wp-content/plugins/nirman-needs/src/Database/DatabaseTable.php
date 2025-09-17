<?php

namespace NirmanNeeds\Database;

defined('ABSPATH') || die;

abstract class DatabaseTable {

    protected string $table_name;
    protected $wpdb;
    protected array $column_formats;

    /**
     * @param array $wpdb WP Database parameter.
     */
    public function __construct($wpdb)
    {
        $this->wpdb = $wpdb;
        $this->set_table_name();
        $this->set_column_formats();
    }

    abstract protected function set_table_name(): void;
    abstract protected function set_column_formats(): void;
    abstract public function get_schema(): string;

    /**
     * @param array $columns Array of columns to be mapped to their formats.
     */
    protected function get_formats(array $columns): array {
        $format = array();
        foreach ($columns as $column) {
            $format[] = $this->column_formats[$column] ?? '%s';
        }
        return $format;
    }
    
    public function create_table(): void
    {
        $charset_collate = $this->wpdb->get_charset_collate();
        $schema = $this->get_schema();
        $msg = $this->wpdb->query( $schema . ' ' . $charset_collate . ';' );
    }
    
    /**
     * @param array $data An array of column => value pairs to be inserted.
     */
    public function insert(array $data): int
    {
        $format = $this->get_formats(array_keys($data));
        $this->wpdb->insert($this->table_name, $data, $format);
        return (int) $this->wpdb->insert_id;
    }

    /**
     * @return array An array of associative arrays of column => value pairs from the table.
     */
    public function get_results(string $query, int|null $id = null): array
    {
        if (empty($id)) {
            return $this->wpdb->get_results($query, ARRAY_A);
        } else {
            return $this->wpdb->get_row($query, ARRAY_A, $id);
        }
    }

    /**
        * @param array $data An array of column => value pairs to be updated.
        * @param array $where An array of column => value pairs which is used to filter the rows to be updated.
     */
    public function update(array $data, array $where = []): int
    {
        $formats = $this->get_formats(array_keys($data));
        $where_formats = $this->get_formats(array_keys($where));
        return (int) $this->wpdb->update($this->table_name, $data, $where, $formats, $where_formats);
    }

    /**
     * @param array $where An array of column => value pairs to filter the rows to be deleted.
     */
    public function delete(array $where = []): int
    {
        $where_formats = $this->get_formats(array_keys($where));
        return (int) $this->wpdb->delete($this->table_name, $where, $where_formats);
    }
}
