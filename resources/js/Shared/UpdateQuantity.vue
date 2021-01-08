<template>
  <div>
        <input :id="id" type="number" min="0" :value="newValue" @input="updateValue"/>
        <button @click="update">Update</button>
  </div>
</template>

<script>

export default {
    props:{
        id: Number,
        initialValue: Number
    },
    data(){
        return{
            newValue:this.initialValue
        }
    },
    methods:{
        updateValue(e){
            this.newValue=e.target.value;
        },
        update(){
            this.$emit("update", this.newValue);
        }
    }
}
</script>
