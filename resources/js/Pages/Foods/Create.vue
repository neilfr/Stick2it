<template>
  <div>
    <form method="POST" @submit.prevent="submit">
        <div class="grid grid-cols-2 gap-2">
            <p class="col-span-2" v-if="errors.description">{{errors.description}}</p>
            <label class="p-2" for="description">Description:</label>
            <input class="border rounded" id="description" type="text" v-model="food.description">
            <p class="col-span-2" v-if="errors.alias">{{errors.alias}}</p>
            <label class="p-2" for="alias">Alias:</label>
            <input class="border rounded" id="alias" type="text" v-model="food.alias"/>
            <p class="col-span-2" v-if="errors.kcal">{{errors.kcal}}</p>
            <label class="p-2" for="KCal">KCal:</label>
            <input class="border rounded" id="kcal" type="number" v-model="food.kcal" min="0"/>
            <p class="col-span-2" v-if="errors.protein">{{errors.protein}}</p>
            <label class="p-2" for="Protein">Protein:</label>
            <input class="border rounded" id="protein" type="number" v-model="food.protein" min="0"/>
            <p class="col-span-2" v-if="errors.fat">{{errors.fat}}</p>
            <label class="p-2" for="Fat">Fat:</label>
            <input class="border rounded" id="fat" type="number" v-model="food.fat" min="0"/>
            <p class="col-span-2" v-if="errors.carbohydrate">{{errors.carbohydrate}}</p>
            <label class="p-2" for="Carbohydrate">Carbohydrate:</label>
            <input class="border rounded" id="carbohydrate" type="number" v-model="food.carbohydrate" min="0"/>
            <p class="col-span-2" v-if="errors.potassium">{{errors.potassium}}</p>
            <label class="p-2" for="Potassium">Potassium:</label>
            <input class="border rounded" id="potassium" type="number" v-model="food.potassium" min="0"/>
            <p v-if="errors.base_quantity">{{errors.base_quantity}}</p>
            <label class="p-2" for="Quantity">Quantity:</label>
            <input class="border rounded" id="base_quantity" type="number" v-model="food.base_quantity" min="0"/>
        </div>
    </form>
    <button @click="store">Save</button>
    <button>Cancel</button>
  </div>
</template>

<script>

export default {
    props:{
        errors: Object,
        user: Object,
    },
    data() {
        return {
            food:{
                description: '',
                alias: '',
                kcal: 0,
                fat: 0,
                protein: 0,
                carbohydrate: 0,
                potassium: 0,
                favourite: true,
                base_quantity: 0,
                foodsource_id: 2,
                foodgroup_id: 26,
                user_id: this.user.id
            }
        }
    },
    methods: {
        store(){
            console.log("food",this.food);
            this.$inertia.post(
                this.$route("foods.store"), this.food
            ).then(()=>{
                console.log("respond!");
                console.log("errors", this.errors.description);
            });
        }
    }
}
</script>
