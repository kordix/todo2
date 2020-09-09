<template>
<div>
    <div v-if="modelname.length > 0">
        <table>
            <tr>
                <td id="1" v-on:drop="drop($event)" ondragover="allowDrop(event)">
                    <div v-for="elem in daneFilter.filter((el)=>el.column==1)" draggable="true" v-on:dragstart="drag($event,elem.id)"  >
                        <input type="checkbox" v-model="elem.status" @click="done(elem.id)">
                        <label for="">{{elem.title}}</label>
                        <button @click="mydestroy(elem.id)" v-if="destroyMode">Usuń</button>
                        <button @click="hide(elem.id)" v-if="destroyMode">Archiwizuj</button>
                    </div>
                </td>
                <td id="2"  v-on:drop="drop($event)"  ondragover="allowDrop(event)">
                    <div v-for="elem in daneFilter.filter((el)=>el.column==2)" draggable="true" v-on:dragstart="drag($event,elem.id)">
                        <input type="checkbox" v-model="elem.status" @click="done(elem.id)">
                        <label for="">{{elem.title}}</label>
                        <button @click="mydestroy(elem.id)" v-if="destroyMode">Usuń</button>
                        <button @click="hide(elem.id)" v-if="destroyMode">Archiwizuj</button>

                    </div>
                </td>
                <td id="3"  v-on:drop="drop($event)"  ondragover="allowDrop(event)">
                    <div v-for="elem in daneFilter.filter((el)=>el.column==3)" draggable="true" v-on:dragstart="drag($event,elem.id)">
                        <input type="checkbox" v-model="elem.status" @click="done(elem.id)">
                        <label for="">{{elem.title}}</label>
                        <button @click="mydestroy(elem.id)" v-if="destroyMode">Usuń</button>
                        <button @click="hide(elem.id)" v-if="destroyMode">Archiwizuj</button>
                    </div>
                </td>
            </tr>
        </table>

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
                    <input v-model="cruddata.title">
                    <input v-model="cruddata.column">

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
    <button id="kolko" @click="destroyMode=true"></button>
</div>
</template>

<script>

import {
    EventBus
} from "../event-bus.js";

export default {
    props: ["modelname"],
    data() {
        return {
            dane: [],
            hidden: ["created_at", "updated_at", "category_id"],
            mode: "create",
            editid: null,
            cruddata: {
                column: 2
            },
            destroyMode: false
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
        async hide(id){
            let self = this;
            let one = self.dane.find((el) => el.id == id);
            one.visible = !one.visible;
          

              await axios.patch("/" + self.modelname.toLowerCase() + "/" + id, {
                    visible: one.visible
            })
        },
        edit(id) {
            this.editid = id;
            this.mode = "edit";
            let freshobject = {};
            freshobject = Object.create(this.dane.find(el => el.id == id));
            this.cruddata = freshobject;
        },
        async done(id) {
            let self = this;
            let one = self.dane.find((el) => el.id == id);
            await axios.patch("/" + self.modelname.toLowerCase() + "/" + id, {
                status: one.status
            })
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
        },

        drag(ev,id) {
            // console.log('id',ev.target.id);
            // console.log(ev.target.id);
          this.editid = id;
            ev.dataTransfer.setData("Text", ev.target.id);
        },

        drop(ev) {
            //var data = ev.dataTransfer.getData("Text");
            console.log(ev.target.id);
            this.cruddata.column = ev.target.id;
            this.update();
           // ev.target.appendChild(document.getElementById(data));
            ev.preventDefault();
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
        },
        daneFilter(){
            return this.dane.filter((el)=>el.visible == 1)
        }
    }
};
</script>

<style scoped>
#kolko {
    border-radius: 5px;
    width: 10px;
    height: 10px;
    border: 1px #999 solid;
    position: absolute;
    left: 20px;
    bottom: 20px;
}

#kolko:hover {
    background: red
}

td {
    /* border: 1px black solid; */
    padding: 0px 200px 0px 0px;
}


#div1 {
  width: 350px;
  height: 70px;
  padding: 10px;
  border: 1px solid #aaaaaa;
}

</style>
