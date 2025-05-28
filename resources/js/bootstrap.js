import axios from 'axios';
import Swal from 'sweetalert2';
window.Swal = Swal;
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
