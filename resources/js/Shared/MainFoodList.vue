<template>
  <div>
      <table class="table-fixed w-full mt-2">
            <tr>
                <th class="w-5">Fav</th>
                <th>Alias</th>
                <th class="w-1/3">Description</th>
                <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">KCal</th>
                <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Protein</th>
                <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Fat</th>
                <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Carbohydrate</th>
                <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Potassium</th>
                <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Quantity</th>
                <th class="text-left translate-x-4 transform -rotate-45 origin-bottom-left">Actions</th>
            </tr>
            <tr v-for="food in foods" :key="food.id" class="odd:bg-gray-100">
                <td class="w-5">
                    <input
                        type="checkbox"
                        :id="food.id"
                        :value="food.favourite"
                        :checked="food.favourite"
                        @change="favourite(food)"
                    />
                </td>
                <td class="w-5 text-center">{{food.alias}}</td>
                <td class="truncate">{{food.description}}</td>
                <td>{{food.kcal}}</td>
                <td>{{food.protein}}</td>
                <td>{{food.fat}}</td>
                <td>{{food.carbohydrate}}</td>
                <td>{{food.potassium}}</td>
                <td>{{food.base_quantity}}</td>
                <td class="text-center flex justify-between">
                    <button v-if="food.editable" @click="edit(food)" :id="food.id">
                        <img class="w-6" src="/images/edit-pencil.svg">
                    </button>
                    <button v-if="food.editable" @click="destroy(food)" :id="food.id">
                        <img class="w-6" src="/images/trash.svg">
                    </button>
                    <button v-if="!food.editable" @click="view(food)" :id="food.id">
                        <img class="w-6" src="/images/view-show.svg">
                    </button>
                </td>
            </tr>
        </table>
  </div>
</template>

<script>
export default {
    props:{
        foods: Array,
    },
    methods:{
        favourite (food) {
            this.$emit('favourite', food);
        },
        view (food) {
            this.$emit('view', food);
        },
        edit (food) {
            this.$emit('edit', food);
        },
        destroy (food) {
            this.$emit('destroy', food);
        }
    }
}
</script>
<style scoped>
    td {
        padding:0.5rem;
    }
</style>
