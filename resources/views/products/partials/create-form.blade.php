<x-product-form 
    :categories="$categories"
    :action="route('products.store')"
    method="POST"
    mode="create"
/>
