<template>
  <div>
    <div v-if="modelname.length > 0">
   
      <div v-for="elem in dane">
          <input type="checkbox"  v-model="elem.status" @click="done(elem.id)"> 
          <label for="">{{elem.title}}</label>
      </div>    
    </div>
    <div v-else>Zapodaj nazwę modelu by użyć komponentu Read</div>

    <div id="edit">
      <div>
        <p v-if="mode=='edit'">
          <b>Edytuj rekord</b>
        </p>
        <p v-else>
          <b>Stwórz nowy rekord</b>
        </p>

        <div>
          <!-- <input type="hidden" class="form-control" :value="cruddata.id" name="id"> -->

          <!-- TU WSTAW HTML -->

          <div class="form-group">
            <label>Todo:</label>
            <input v-model="cruddata.title" >
          </div>
          <!-- <div class="form-group">
            <label>description</label>
            <input v-model="cruddata.description" >
           </div>
           <div>
          <label>category_id</label>
          <input v-model="cruddata.category_id" >
          </div> -->

          <!-- koniec html -->
          <button type="button" class="btn btn-primary" @click="add" v-if="mode=='create'">zapisz</button>
          <button type="button" class="btn btn-primary" @click="update" v-if="mode=='edit'">Zmień</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { EventBus } from "../event-bus.js";

export default {
  props: ["modelname"],
  data() {
    return {
      dane: [],
      hidden: ["created_at", "updated_at", "category_id"],
      mode: "create",
      editid: null,
      cruddata: {}
    };
  },
  methods: {
    getData() {
      let self = this;
      //axios.get("/category").then(res => console.log(res));
      axios
        .get("/" + self.modelname.toLowerCase())
        .then(res => (self.dane = res.data));
    },
    mydestroy(id) {
      let self = this;
      axios
        .delete("/" + self.modelname.toLowerCase() + "/" + id)
        .then(res => self.getData());
    },
    edit(id) {
      this.editid = id;
      this.mode = "edit";
      let freshobject = {};
      freshobject = Object.create(this.dane.find(el => el.id == id));
      this.cruddata = freshobject;
    },
    done(id){
      let self = this;
      setTimeout(function(){
          let one = self.dane.find((el) => el.id == id);
      console.log(one);
      
      //this.cruddata.status = one.status;

      console.log(one.status);
      
      axios.patch("/" + self.modelname.toLowerCase() + "/" + id, {status:one.status})


      },1000)
     
    
     


  
      // this.cruddata 
      
      
    },
    update() {
      let self = this;
      axios
        .patch(
          "/" + self.modelname.toLowerCase() + "/" + self.editid,
          this.cruddata
        )
        .then(res => self.getData());
    },
    add() {
      let self = this;
      axios
        .post("/" + this.modelname.toLowerCase(), this.cruddata)
        .then(res => self.getData());
    },
    reset() {
      this.cruddata = {};
    }
  },
  mounted() {
    this.getData();
    EventBus.$on("reload", payload => {
      console.log("działa");
      this.getData();
    });
  },
  computed: {
    heads() {
      let self = this;
      // return typeof(this.dane[0])
      if (typeof this.dane[0] == "object") {
        return Object.keys(this.dane[0]).filter(
          el => self.hidden.indexOf(el) == -1
        );
      } else {
        return [];
      }
    }
  }
};
</script>