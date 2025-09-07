<x-product-form 
    :product="$product"
    :categories="$categories"
    :action="route('products.update', $product)"
    method="POST"
    mode="edit"
/>
