package com.example.android.ui.appart

import android.content.Context
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.BaseAdapter
import android.widget.ImageView
import android.widget.TextView
import com.example.android.R
import com.squareup.picasso.Picasso

class AppartAdapter : BaseAdapter {
    var context: Context
    var appart:MutableList<Appart>

    constructor(context: Context, appart: MutableList<Appart>) : super() {
        this.context = context
        this.appart = appart
    }

    override fun getCount(): Int {
        return this.appart.size
    }

    override fun getItem(position: Int): Any {
        return this.appart.get(position)
    }

    override fun getItemId(position: Int): Long {
        return this.appart.get(position).id
    }

    override fun getView(position: Int, convertView: View?, parent: ViewGroup?): View {
        var v: View
        if (convertView==null) {
            var inflater = LayoutInflater.from(this.context)
            v = inflater.inflate(R.layout.appart_block,null)
        } else {
            v=convertView
        }
        var current: Appart = getItem(position) as Appart

        v.findViewById<TextView>(R.id.appbltitle).text = current.titre
        Log.i("image",current.image)
        Picasso.get().load("https://pariscaretakerservices.fr/images/appartements/"+current.image).resize(100,100).into(v.findViewById<ImageView>(
            R.id.appblimg
        ))
        return v
    }
}