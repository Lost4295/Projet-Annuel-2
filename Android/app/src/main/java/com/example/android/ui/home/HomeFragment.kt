package com.example.android.ui.home

import android.Manifest.permission
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ListView
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import com.android.volley.AuthFailureError
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.android.AppartAdapter
import com.example.android.MiniAppart
import com.example.android.R
import com.example.android.databinding.FragmentHomeBinding
import com.google.android.material.snackbar.Snackbar
import org.json.JSONArray
import org.json.JSONException
import org.json.JSONObject


class HomeFragment : Fragment() {

    private var _binding: FragmentHomeBinding? = null

    // This property is only valid between onCreateView and
    // onDestroyView.
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        val homeViewModel =
            ViewModelProvider(this).get(HomeViewModel::class.java)

        _binding = FragmentHomeBinding.inflate(inflater, container, false)
        val root: View = binding.root

        val textView: TextView = binding.textHome
        homeViewModel.text.observe(viewLifecycleOwner) {
            textView.text = it
        }


        return root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        var queue = Volley.newRequestQueue(requireContext())
        var token: String = ""
        val jsonBody = JSONObject()
        try {
            jsonBody.put("email", "api@pcs.fr")
            jsonBody.put("password", "api")
        } catch (e: JSONException) {
            e.printStackTrace()
        }
        var adapter :AppartAdapter? = null
        view.findViewById<ListView>(R.id.lv_appart).adapter = null
        var req3 : JsonObjectRequest = object : JsonObjectRequest(
            Request.Method.GET,
            "https://api.pariscaretakerservices.fr/appartements",
            null,
            Response.Listener{ content ->
                var jsonarr: JSONArray = content.getJSONArray("hydra:member")
                var listapp:MutableList<MiniAppart>  = mutableListOf<MiniAppart>()
                for ( i in 0 .. jsonarr.length()-1){
                    var cur_jso =jsonarr.getJSONObject(i)
                    var imgarr: JSONArray=  cur_jso.getJSONArray("images")
                    var b = MiniAppart(cur_jso.getLong("id"),
                        cur_jso.getString("shortDesc"),
                        cur_jso.getInt("price"),
                        cur_jso.getString("address"),
                        cur_jso.getString("city"),
                        cur_jso.getString("country"),
                        imgarr.get(0).toString(),
                        cur_jso.getString("bailleur"),
                        cur_jso.getString("titre"))
                    listapp.add(b)
                }
                Log.i("herhdehtrz", listapp.toString())
                adapter = AppartAdapter(requireContext(), listapp)
                Log.d("Adapter", adapter.toString())
                view.findViewById<ListView>(R.id.lv_appart).adapter = adapter
            },
            Response.ErrorListener { error ->
                if (error.networkResponse != null) {
                    val statusCode = error.networkResponse.statusCode
                    val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                    Log.e("hhn", errorMessage)
                    Toast.makeText(requireContext(), "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                } else {
                    Toast.makeText(requireContext(), error.message, Toast.LENGTH_LONG).show()
                    Toast.makeText(requireContext(), error.toString(), Toast.LENGTH_LONG).show()
                    Log.e("herhdertfee", error.toString())

                }
            }
        ){
            @Throws(AuthFailureError::class)
            override fun getHeaders(): Map<String, String> {
                var params: MutableMap<String, String> = mutableMapOf<String,String>() ;
                var ah= "Bearer"
                var final = "$ah $token"
                params.put("Authorization",final)
                params.put("Accept","*/*")
                params.put("Content-Type","application/ld+json")

                //..add other headers
                return params
            }
        };
        var req2 = JsonObjectRequest(
            Request.Method.POST,
            "https://api.pariscaretakerservices.fr/auth",
            jsonBody,
            Response.Listener{ content ->
                try {
                    token = content.getString("token")
                    queue.add(req3)
                } catch (e: JSONException){
                    e.printStackTrace()
                }
            },
            Response.ErrorListener { error ->
                if (error.networkResponse != null) {
                    val statusCode = error.networkResponse.statusCode
                    val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                    Toast.makeText(requireContext(), "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                } else {
                    Toast.makeText(requireContext(), error.toString(), Toast.LENGTH_LONG).show()
                }
                Log.e("herhe", error.toString())
            }
        )
        queue.add(req2)

        view.findViewById<ListView>(R.id.lv_appart).setOnItemClickListener { parent, view, position, id ->
            val item = parent.getItemAtPosition(position) as MiniAppart
            Snackbar.make(view, item.toString(), Snackbar.LENGTH_LONG)
                .setAction("Action", null).show()
        }

        super.onViewCreated(view, savedInstanceState)

    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}