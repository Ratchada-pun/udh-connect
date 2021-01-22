<?php

use kartik\form\ActiveForm;
use yii\helpers\Url;

$this->title = "สถานะคิว";


$this->registerCss(<<<CSS
.card {
    box-shadow: 0 2px 7px 0 rgba(5, 34, 97, 0.1);
    border-radius: 0.48rem;
}
.card-header {
  padding: 8px 16px;
}
.card-title {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  width: 100%;
  font-size: 1.2rem;
}
.card-title-time,
.card-title-date {
  display: inline-block;
  color: #2786fb !important;
  font-weight: 500;
}
.card-footer .btn-success {
  font-size: 12px;
  border-radius: 56px;
}
.card-body ul {
    font-size: 14px;
    list-style: none;
    /* margin-block-start: 0; */
    /* margin-block-end: 0; */
    padding-inline-start: 0;
}
.kt-input-icon {
  margin-bottom: 10px;
}
.container {
    padding: 0;
}
.container-content {
    padding: 15px 0 0 0;
}
.list-group-item.active {
    background-color: #ff518a;
    border-color: #ff518a;
}

.btn {
    display: inline-block;
}

.card-body {
    padding: 0;
}
.btn-radius {
    border-radius: 56px;
}
.card-header.header-gray,
.card-footer.footer-gray{
    background: #e2e2e2!important;
    border: 1px solid rgba(0, 0, 0, 0.125)!important;
}

CSS
);

?>
<div class="card">
    <div class="card-header text-white bg-danger border-0  text-center">
        <div class="media p-6">
            <div class="media-body">
                <p class="btn-flat m-b-30 m-t-30">
                    <strong class="">
                        <p style="font-size: 16pt;margin-top:5px;">
                            สถานะคิว
                        </p>
                    </strong>
                </p>
            </div>
        </div>
    </div>
    <div class="card-body" style="background: #f6f6f6;">
        <ul class="no-margin mt-10" style="list-style: none;padding-inline-start: 40px;font-size: 16px;">
            <li><strong>ชื่อ:</strong> <?= $profile ? $profile['firstName'] . ' ' . $profile['lastName'] : '-' ?></li>
            <li><strong>NH:</strong> <?= $profile ? $profile['hn'] : '-' ?> </li>
        </ul>

    </div>
</div>
<div id="app" class="d-flex align-content-center flex-wrap">

    <!-- <div class="form-group w-100 mt-10">
        <input v-model="filterKey" class="form-control" type="text" placeholder="ค้นหา...." />
    </div> -->

    <div class="container">
        <div class="container-content">

            <h4 v-if="!filteredQueueList.length" class="text-center text-danger" style="font-size: 20px;">
                ไม่พบข้อมูล สถานะคิวของท่าน!!
            </h4>

            <div v-for="(item, key) in filteredQueueList" :key="key" class="card card-shadow  mb-4">
                <div :class="['card-header',{'header-gray': item.queue_status_id === '4'}]" style="padding: 12px 16px;border: 1px solid #e5e9ec;">
                    <div class="card-title">
                        <span class="card-title-date" style="font-size: 14px;font-weight: 600;">
                            คิวของคุณ : {{ item.queue_no }}
                        </span>
                        <div class="card-title-time" style="font-size: 14px;font-weight: 600;">
                            <span>
                                คิวกำลังเรียก : {{ item.last_queue }}
                            </span>
                            <!-- <span>
                                <i class="far fa-calendar-alt"></i>
                                {{ item.queue_date }}
                            </span> -->
                            <!-- <span class="hidden" style="color: #8F8F8F;">
                                <i class="far fa-clock"></i> เวลา:
                            </span>
                            {{ item.queue_time }} น. -->
                        </div>
                    </div>
                </div>
                <div class="card-body" style="background: #f6f6f6;padding: 12px 16px;">
                    <ul class="no-margin">
                        <li><strong>{{ item.service_type_name }}</strong> </li>
                        <li><strong>แผนก : </strong>{{ item.service_name }}</li>
                        <li><strong>ห้องตรวจ/ช่องบริการ :</strong> {{ item.counter_service_name }} </li>
                        <li v-if="item.doctor_name"><strong>แพทย์ : </strong> {{ item.doctor_name ? item.doctor_title + item.doctor_name : ''  }}</li>
                    </ul>
                </div>
                
                <div :class="['card-footer',{'footer-gray': item.queue_status_id === '4'}]">
                    
                   <button type="button" :class="getStatusClass(item.queue_status_id)">
                        สถานะ : {{ item.queue_status_name }}
                    </button> 
                    
                    <button type="button" class="btn btn-warning btn-sm pull-right btn-radius">   <!--จำนวนคิวที่รอนับตามจำนวนคิวจากหน้าจอเรียกคิว--->
                        คิวรอ : {{ item.count }} 
                    </button>
                </div>


            </div>
        </div>
    </div>
</div>

<?php
echo $this->render('menu');
?>

<?php
$this->registerJsFile(
    '@web/js/socket.io.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    'https://cdn.jsdelivr.net/npm/vue/dist/vue.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    'https://momentjs.com/downloads/moment.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJsFile(
    'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/locale/th.min.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]
);
$this->registerJs(<<<JS

//moment.locale('th');
var app = new Vue({
    el: '#app',
    data: {
        queueList: [],
        displays: [],
        count: {},
        filterKey: ''
    },
    computed: {
        hn: function() {
            var params = this.getQueryParams(window.location.search)
            return params.hn
        },
        filteredQueueList: function() {
            var _this = this
            var filterKey = this.filterKey && this.filterKey.toLowerCase();
            var queueList = _this.queueList
            var examinations = _this.displays.examinations || []
            var historys = _this.displays.historys || []
            if (filterKey) {
                queueList = queueList.filter(function(row) {
                return Object.keys(row).some(function(key) {
                  return (
                    String(row[key])
                      .toLowerCase()
                      .indexOf(filterKey) > -1
                  );
                });
              });
            }
            queueList = queueList.map(row => {
                var history = historys.find(r => Number(r.counter_service_id)  === Number(row.counter_service_id))
                var examination = examinations.find(r => Number(r.counter_service_id)  === Number(row.counter_service_id1))
                return _this.updateObject(row, { //2 = กำลังเรียก,4 = เสร็จสิ้น
                    count: (row.queue_status_id === '2' || row.queue_status_id === '4') ? 0 : _this.count[row.queue_detail_id] || 0, 
                    last_queue: history ? (historys[0].queue_no || '-') : (examination ? (examinations[0].queue_no || '-') : '-')
                })
            })
            return queueList
        },
        ids: function() {
            return this.queueList.map(row => Number(row.queue_detail_id))
        },
        counter_service_ids: function() {
            return this.queueList.map(row => row.counter_service_id || row.counter_service_id1)
        }
    },
    mounted: function () {
        this.\$nextTick(function () {
            this.fetchDataQueueList()
        })
    },
    methods: {
        getQueryParams: function (url) {
            var pos = url.indexOf('?');
            if (pos < 0) {
                return {};
            }

            var pairs = $.grep(url.substring(pos + 1).split('#')[0].split('&'), function (value) {
                return value !== '';
            });
            var params = {};

            for (var i = 0, len = pairs.length; i < len; i++) {
                var pair = pairs[i].split('=');
                var name = decodeURIComponent(pair[0].replace(/\+/g, '%20'));
                var value = decodeURIComponent(pair[1].replace(/\+/g, '%20'));
                if (!name.length) {
                    continue;
                }
                if (params[name] === undefined) {
                    params[name] = value || '';
                } else {
                    if (!$.isArray(params[name])) {
                        params[name] = [params[name]];
                    }
                    params[name].push(value || '');
                }
            }

            return params;
        },
        fetchDataQueueList: async function() {
            try {
                var result = await axios.get('/app/appoint/queue-list?hn=' + this.hn)
                this.queueList = result.data
                this.getCountData()
                this.fetchDataQueueWait()
            } catch (error) {
                swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || '',
                })
            }
        },
        getCountData: async function() {
            try {
                for (let i = 0; i < this.queueList.length; i++) {
                    var queue = this.queueList[i];
                    var result = await axios.post('https://queue.udhconnect.info/api/v1/queue/count-queue-wait', queue)
                    var obj = {}
                    obj[queue.queue_detail_id] = result.data.data.count || 0
                    var count = result.data.data.count || 0
                    if(count > 0 && count < 4){
                        swal.fire({
                            icon: 'warning',
                            title: 'แจ้งเตือน!',
                            text: `รออีก \${count} คิว`,
                            showConfirmButton: true,
                            confirmButtonText: 'ตกลง'
                        })
                    }
                    this.count = this.updateObject(this.count, obj)
                }
            } catch (error) {
                swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || '',
                })
            }
        },
        fetchDataQueueWait: async function() {
            try {
                var counter_service_ids = this.queueList.map(row => row.counter_service_id || row.counter_service_id1)
                var service_ids = this.queueList.map(row => row.service_id)
                var result = await axios.post('https://queue.udhconnect.info/api/v1/display/queue-display-today', {
                    counter_service_ids: counter_service_ids,
                    service_ids: service_ids
                })
                this.displays = result.data.data
            } catch (error) {
                swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || '',
                })
            }
        },
        updateObject: function(oldObject, updatedProperties) {
            return {
                ...oldObject,
                ...updatedProperties
            }
        },
        getStatusClass: function(status) {
            var statusClass = 'btn btn-sm'
            switch (status) {
                case '1': //รอเรียก
                    statusClass = 'btn btn-info btn-sm btn-radius'
                    break;
                case '2': //กำลังเรียก
                    statusClass = 'btn btn-success btn-sm btn-radius'
                    break;
                case '3'://พักคิว
                    statusClass = 'btn btn-warning btn-sm btn-radius'
                    break;
                case '4'://เสร็จสิ้น
                    statusClass = 'btn btn-success btn-sm btn-radius'
                    break;
                default:
                statusClass = 'btn btn-success btn-sm btn-radius'
                    break;
            }
            return statusClass
        }
   }
})

var socket = io('https://queue.udhconnect.info', { path: '/node-api/socket.io' });
socket.on('call wait', function(data){
    if(data.data.queue_detail){
        var queue_detail = data.data.queue_detail
        var counter = data.data.counter
        if (app.ids.includes(Number(queue_detail.queue_detail_id))) {
            swal.fire({
              icon: 'warning',
              title: 'ถึงคิวคุณแล้วค่ะ!',
              text: `กรุณาเชิญที่ \${counter.counter_service_name}`,
              showConfirmButton: true,
              confirmButtonText: 'ตกลง'
            })
        } else {
            app.getCountData()
            if(app.counter_service_ids.includes(Number(counter.counter_service_id))) {
                app.fetchDataQueueWait()
            }
        }
    }
});
JS
);
?>