<template>
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col">
          <h3 class="mb-0">تراخيص البرمجيات</h3>
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-primary btn-sm" @click="toggleModule">{{module.icon}}</button>
        </div>
      </div>
    </div>
    <div class="card-body" v-if="module.display">
      <div class="accordion" id="software_licence_arabic">
        <div class="card" v-for="(software_licence,index) in software_licences" :key="index">
          <div class="col-md-12" v-if="editsoftware_licences.status && editsoftware_licences.index==index">
            <div class="form-group">
              <div class="input-group input-group-merge">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
                <input class="form-control" type="text" v-model="software_licence.title">
              </div>
            </div>
            <div class="form-group">
              <textarea class="form-control" rows="8" placeholder="CONTENT GOES HERE" v-model="software_licence.content"></textarea>
            </div>
            <div class="text-right">
              <button type="button" class="btn btn-success btn-sm" @click="update">Update</button>
              <button type="button" class="btn btn-danger btn-sm" @click="cancelUpdate(index)">Cancel</button>
            </div>
          </div>
          <template v-else>
            <div class="card-header" :id="'heading-'+index" data-toggle="collapse" :data-target="'#collapse-'+index" aria-expanded="false" aria-controls="collapseOne">
              <h5 class="mb-0">{{index+1}}. {{software_licence.title}}</h5>
            </div>
            <div :id="'collapse-'+index" class="collapse" :aria-labelledby="'heading-'+index" data-parent="#software_licence_arabic">
              <div class="card-body">
                <p>{{software_licence.content}}</p>
                <div class="text-right">
                  <button type="button" class="btn btn-info btn-sm" @click="edit(index)">Edit</button>
                  <button type="button" class="btn btn-danger btn-sm" @click="deletesoftware_licence(index)">Delete</button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
      <div class="row" v-if="addsoftware_licences">
        <div class="col-md-12" >
          <div class="form-group">
            <div class="input-group input-group-merge">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
              </div>
              <input class="form-control" type="text" v-model="newsoftware_licences.title">
            </div>
          </div>
          <div class="form-group">
            <textarea class="form-control" rows="8" placeholder="CONTENT GOES HERE" v-model="newsoftware_licences.content"></textarea>
          </div>
        </div>
      </div>
      <div class="text-right">
        <button type="button" class="btn btn-primary btn-sm" @click="addsoftware_licence" v-if="!addsoftware_licences">Add More</button>
        <button type="button" class="btn btn-info btn-sm" @click="savesoftware_licence" v-if="addsoftware_licences">Save</button>
        <button type="button" class="btn btn-danger btn-sm" @click="cancelAdd" v-if="addsoftware_licences">Cancel</button>
      </div>
    </div>
  </div>
</template>


<script>

  export default{
    data(){
      return{
        addsoftware_licences:false,
        editsoftware_licences:{
          index:null,
          status:false
        },
        editBtn:true,
        newsoftware_licences:{
          title:'',
          content:''
        },
        errors:{},
      }
    },
    methods:{
      edit(index){
        this.editsoftware_licences = {
          index:index,
          status:true
        }
        this.editBtn = false
      },
      cancelUpdate(index){
        this.editsoftware_licences = {
          index:null,
          status:false
        }
        this.editBtn = true
        this.$store.dispatch('getAppDefaults')
      },
      update(){
        this.save()
      },
      deletesoftware_licence(index){
        this.$swal({
          title: 'Are you sure?',
          text: "You may not undo this",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.value) {
            this.software_licences.splice(index, 1)
            this.save()
          }
        })
      },
      addsoftware_licence(){
        this.addsoftware_licences = true
      },
      cancelAdd(){
        this.addsoftware_licences = false,
        this.newsoftware_licences = {
          title:'',
          content:''
        }
      },
      savesoftware_licence(){
        this.software_licences.push(this.newsoftware_licences)
        this.newsoftware_licences = {
            title:'',
            content:''
          }
        this.addsoftware_licences = false,
        this.save()
      },
      save(){
        var data = {
          saveType: 'software_licences',
          software_licences: {
            en: this.software_licencesEnglish,
            ar: this.software_licences
          }
        }
        axios.post('/appDefaults',data)
        .then((response) => {
          this.editsoftware_licences = {
            index:null,
            status:false
          }
          this.editBtn = true
          this.$store.dispatch('getAppDefaults')
          showNotify('success','Saved Successfully')
        })
        .catch((error) => {
          for (var prop in error.response.data.errors) {
            showNotify('danger',error.response.data.errors[prop])
          }  
        })
      },
      toggleModule(){
        this.$parent.toggleModule('software_licences')
      }
    },
    computed: {
      module(){
        return this.$parent.modules.software_licences
      },
      software_licences(){
        return this.$parent.appDefaults.software_licences.ar
      },
      software_licencesEnglish(){
        return this.$parent.appDefaults.software_licences.en
      }
    },
  }

</script>