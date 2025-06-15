<?php

namespace App\Http\Controllers;

use App\Models\Product; // Pastikan Anda mengimpor model Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session; // Digunakan untuk mengelola sesi

class CartController extends Controller
{
    /**
     * Menambahkan produk ke keranjang belanja.
     *
     * @param  \App\Models\Product  $product
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(Request $request, Product $product)
    {
        $quantity = $request->input('quantity', 1); // Ambil kuantitas dari form, default 1

        // Dapatkan keranjang saat ini dari sesi
        $cart = Session::get('cart', []);

        // Hasilkan ID unik untuk item di keranjang (gunakan ID produk untuk sederhana)
        $cartItemId = $product->id;

        // Jika produk sudah ada di keranjang, tambahkan kuantitasnya
        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] += $quantity;
        } else {
            // Jika belum ada, tambahkan produk baru ke keranjang
            $cart[$cartItemId] = [
                'product_id' => $product->id,
                'name'       => $product->title, // Asumsi nama produk adalah 'title'
                'price'      => $product->price,
                'quantity'   => $quantity,
                'image'      => $product->image_url, // Asumsi ada image_url
            ];
        }

        // Simpan kembali keranjang ke sesi
        Session::put('cart', $cart);

        return redirect()->back()->with('success', $product->title . ' telah ditambahkan ke keranjang!');
    }

    /**
     * Menampilkan isi keranjang belanja.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $cart = Session::get('cart', []);
        return view('cart.show', compact('cart'));
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $productId (Menggunakan ID produk sebagai rowId)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $productId)
    {
        $quantity = $request->input('quantity');
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            if ($quantity > 0) {
                $cart[$productId]['quantity'] = $quantity;
                Session::put('cart', $cart);
                return redirect()->back()->with('success', 'Kuantitas produk berhasil diperbarui.');
            } else {
                // Jika kuantitas 0 atau kurang, hapus item dari keranjang
                unset($cart[$productId]);
                Session::put('cart', $cart);
                return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
            }
        }
        return redirect()->back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Menghapus item dari keranjang.
     *
     * @param  string  $productId (Menggunakan ID produk sebagai rowId)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove($productId)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);
            return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
        }

        return redirect()->back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Mengosongkan seluruh keranjang.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clear()
    {
        Session::forget('cart');
        return redirect()->back()->with('success', 'Keranjang belanja telah dikosongkan.');
    }
}