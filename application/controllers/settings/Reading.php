<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CMS Sekolahku | CMS (Content Management System) dan PPDB/PMB Online GRATIS
 * untuk sekolah SD/Sederajat, SMP/Sederajat, SMA/Sederajat, dan Perguruan Tinggi
 * @version    2.4.6
 * @author     Anton Sofyan | https://facebook.com/antonsofyan | 4ntonsofyan@gmail.com | 0857 5988 8922
 * @copyright  (c) 2014-2019
 * @link       https://sekolahku.web.id
 *
 * PERINGATAN :
 * 1. TIDAK DIPERKENANKAN MENGGUNAKAN CMS INI TANPA SEIZIN DARI PIHAK PENGEMBANG APLIKASI.
 * 2. TIDAK DIPERKENANKAN MEMPERJUALBELIKAN APLIKASI INI TANPA SEIZIN DARI PIHAK PENGEMBANG APLIKASI.
 * 3. TIDAK DIPERKENANKAN MENGHAPUS KODE SUMBER APLIKASI.
 */

class Reading extends Admin_Controller {

	/**
	 * Class Constructor
	 *
	 * @return Void
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('m_settings');
		$this->pk = M_settings::$pk;
		$this->table = M_settings::$table;
	}

	/**
	 * Index
	 * @return Void
	 */
	public function index() {
		$this->vars['title'] = 'Pengaturan Membaca';
		$this->vars['settings'] = $this->vars['reading_settings'] = true;
		$this->vars['content'] = 'settings/reading';
		$this->load->view('backend/index', $this->vars);
	}

	/**
	 * Pagination
	 * @return Object
	 */
	public function pagination() {
		if ($this->input->is_ajax_request()) {
			$keyword = trim($this->input->post('keyword', true));
			$page_number = _toInteger($this->input->post('page_number', true));
			$limit = _toInteger($this->input->post('per_page', true));
			$offset = ($page_number * $limit);
			$query = $this->m_settings->get_where($keyword, 'reading', 'rows', $limit, $offset);
			$total_rows = $this->m_settings->get_where($keyword, 'reading');
			$total_page = $limit > 0 ? ceil(_toInteger($total_rows) / _toInteger($limit)) : 1;
			$this->vars['total_page'] = _toInteger($total_page);
			$this->vars['total_rows'] = _toInteger($total_rows);
			$this->vars['rows'] = $query->result();
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
	}
	
	/**
	 * Save | Update
	 * @return Object
	 */
	public function save() {
		if ($this->input->is_ajax_request()) {
			$id = _toInteger($this->input->post('id', true));
			if ($this->validation()) {
				$dataset = $this->dataset();
				$dataset['updated_by'] = __session('user_id');
				$query = $this->model->update($id, $this->table, $dataset);
				$this->vars['status'] = $query ? 'success' : 'error';
				$this->vars['message'] = $query ? 'Data Anda berhasil disimpan.' : 'Terjadi kesalahan dalam menyimpan data';
			} else {
				$this->vars['status'] = 'error';
				$this->vars['message'] = validation_errors();
			}
			$this->output
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($this->vars, JSON_HEX_APOS | JSON_HEX_QUOT))
				->_display();
			exit;
		}
	}

	/**
	 * Dataset
	 * @return Array
	 */
	private function dataset() {
		return [
			'setting_value' => $this->input->post('setting_value', true)
		];
	}

	/**
	 * Validation Form
	 * @return Boolean
	 */
	private function validation() {
		$this->load->library('form_validation');
		$val = $this->form_validation;
		$val->set_rules('setting_value', 'Value', 'trim|required');
		$val->set_error_delimiters('<div>&sdot; ', '</div>');
		return $val->run();
	}
}
