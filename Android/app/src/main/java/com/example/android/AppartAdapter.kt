package com.example.android

import android.content.Context
import android.net.Uri
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.BaseAdapter
import android.widget.ImageView
import android.widget.TextView

class AppartAdapter : BaseAdapter {
    var context: Context
    var appart:MutableList<MiniAppart>

    constructor(context: Context, appart: MutableList<MiniAppart>) : super() {
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
        if (convertView==null){
            var inflater = LayoutInflater.from(this.context)
            v = inflater.inflate(R.layout.appart_block,null)
        } else {
            v=convertView
        }
        var current: Appart = getItem(position) as Appart

        v.findViewById<TextView>(R.id.appbltitle).text = current.titre
        v.findViewById<ImageView>(R.id.appblimg).setImageURI(Uri.parse(current.images[0]))
        return v
    }
}