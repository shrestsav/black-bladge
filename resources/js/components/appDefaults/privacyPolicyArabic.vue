<template>
  <div class="card">
    <div class="card-header">
      <div class="row">
        <div class="col">
          <h3 class="mb-0">سياسة الخصوصية</h3>
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-primary btn-sm" @click="toggleModule">{{module.icon}}</button>
        </div>
      </div>
    </div>
    <div class="card-body" v-if="module.display">
      <div class="accordion" id="privacy_policies_arabic">
        <div class="card" v-for="(privacy_policy,index) in privacy_policies" :key="index">
          <div class="col-md-12" v-if="editprivacy_policies.status && editprivacy_policies.index==index">
            <div class="form-group">
              <div class="input-group input-group-merge">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
                <input class="form-control" type="text" v-model="privacy_policy.title">
              </div>
            </div>
            <div class="form-group">
              <textarea class="form-control" rows="8" placeholder="CONTENT GOES HERE" v-model="privacy_policy.content"></textarea>
            </div>
            <div class="text-right">
              <button type="button" class="btn btn-success btn-sm" @click="update">Update</button>
              <button type="button" class="btn btn-danger btn-sm" @click="cancelUpdate(index)">Cancel</button>
            </div>
          </div>
          <template v-else>
            <div class="card-header" :id="'heading-'+index" data-toggle="collapse" :data-target="'#collapse-'+index" aria-expanded="false" aria-controls="collapseOne">
              <h5 class="mb-0">{{index+1}}. {{privacy_policy.title}}</h5>
            </div>
            <div :id="'collapse-'+index" class="collapse" :aria-labelledby="'heading-'+index" data-parent="#privacy_policies_arabic">
              <div class="card-body">
                <p>{{privacy_policy.content}}</p>
                <div class="text-right">
                  <button type="button" class="btn btn-info btn-sm" @click="edit(index)">Edit</button>
                  <button type="button" class="btn btn-danger btn-sm" @click="deleteprivacy_policy(index)">Delete</button>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
      <div class="row" v-if="addprivacy_policies">
        <div class="col-md-12" >
          <div class="form-group">
            <div class="input-group input-group-merge">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
              </div>
              <input class="form-control" type="text" v-model="newprivacy_policies.title">
            </div>
          </div>
          <div class="form-group">
            <textarea class="form-control" rows="8" placeholder="CONTENT GOES HERE" v-model="newprivacy_policies.content"></textarea>
          </div>
        </div>
      </div>
      <div class="text-right">
        <button type="button" class="btn btn-primary btn-sm" @click="addprivacy_policy" v-if="!addprivacy_policies">Add More</button>
        <button type="button" class="btn btn-info btn-sm" @click="saveprivacy_policy" v-if="addprivacy_policies">Save</button>
        <button type="button" class="btn btn-danger btn-sm" @click="cancelAdd" v-if="addprivacy_policies">Cancel</button>
      </div>
    </div>
  </div>
</template>


<script>

  export default{
    data(){
      return{
        addprivacy_policies:false,
        editprivacy_policies:{
          index:null,
          status:false
        },
        editBtn:true,
        newprivacy_policies:{
          title:'',
          content:''
        },
        errors:{},
      }
    },
    methods:{
      edit(index){
        this.editprivacy_policies = {
          index:index,
          status:true
        }
        this.editBtn = false
      },
      cancelUpdate(index){
        this.editprivacy_policies = {
          index:null,
          status:false
        }
        this.editBtn = true
        this.$store.dispatch('getAppDefaults')
      },
      update(){
        this.save()
      },
      deleteprivacy_policy(index){
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
            this.privacy_policies.splice(index, 1)
            this.save()
          }
        })
      },
      addprivacy_policy(){
        this.addprivacy_policies = true
      },
      cancelAdd(){
        this.addprivacy_policies = false,
        this.newprivacy_policies = {
          title:'',
          content:''
        }
      },
      saveprivacy_policy(){
        this.privacy_policies.push(this.newprivacy_policies)
        this.newprivacy_policies = {
            title:'',
            content:''
          }
        this.addprivacy_policies = false,
        this.save()
      },
      save(){
        var data = {
          saveType: 'privacy_policies',
          privacy_policies: {
            en: this.privacy_policiesEnglish,
            ar: this.privacy_policies
          }
        }
        axios.post('/appDefaults',data)
        .then((response) => {
          this.editprivacy_policies = {
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
        this.$parent.toggleModule('privacy_policies')
      }
    },
    computed: {
      module(){
        return this.$parent.modules.privacy_policies
      },
      privacy_policies(){
        return this.$parent.appDefaults.privacy_policies.ar
      },
      privacy_policiesEnglish(){
        return this.$parent.appDefaults.privacy_policies.en
      }
    },
  }

</script>