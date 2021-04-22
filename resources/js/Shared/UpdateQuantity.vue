<template>
  <div>
        <div class="grid grid-cols-2">
            <label for="id">Quantity:</label>
            <input class="border rounded mb-2" :id="id" type="number" min="0" :value="newValue" @input="updateValue"/>
            <button class="border rounded" @click="update">Update</button>
        </div>
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
