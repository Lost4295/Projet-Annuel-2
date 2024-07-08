package com.example.android.ui.home


import android.content.Context
import android.os.Bundle
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.ListView
import android.widget.ProgressBar
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.navigation.fragment.findNavController
import com.android.volley.AuthFailureError
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.android.MainActivity
import com.example.android.ui.appart.AppartAdapter
import com.example.android.ui.appart.MiniAppart
import com.example.android.R
import com.example.android.databinding.FragmentHomeBinding
import com.example.android.ui.appart.Appart
import com.google.android.material.navigation.NavigationView
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
        val activity = activity as? MainActivity

        val prefs = requireContext().getSharedPreferences("user", 0)
        if (prefs.getString("usrtoken", null) != null) {
            val navView = activity?.findViewById<NavigationView>(R.id.nav_view)
            navView?.findViewById<TextView>(R.id.emailuser)?.text = "Bonjour, "+prefs.getString("rname", null)
        }
        val root: View = binding.root
        return root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        val spinner = view.findViewById<ProgressBar>(R.id.progressBar1);
        val textView: TextView = view.findViewById(R.id.tv_loading)
        val btn = view.findViewById<Button>(R.id.btn_more)
        val lv = view.findViewById<ListView>(R.id.lv_appart)
        spinner.visibility = View.VISIBLE
        textView.visibility = View.VISIBLE
        btn.visibility = View.GONE
        lv.visibility = View.GONE
        var queue = Volley.newRequestQueue(requireContext())
        var token: String = ""
        val jsonBody = JSONObject()
        try {
            jsonBody.put("email", "api@pcs.fr")
            jsonBody.put("password", "api")
        } catch (e: JSONException) {
            e.printStackTrace()
        }
        var adapter : AppartAdapter? = null
        lv.adapter = null
        var req3 : JsonObjectRequest = object : JsonObjectRequest(
            Request.Method.GET,
            "https://api.pariscaretakerservices.fr/appartements",
            null,
            Response.Listener{ content ->
                var jsonarr: JSONArray = content.getJSONArray("hydra:member")
                var jsoninfo: JSONObject = content.getJSONObject("hydra:view")
                var listapp:MutableList<Appart>  = mutableListOf<Appart>()
                for ( i in 0 .. jsonarr.length()-1){
                    var cur_jso =jsonarr.getJSONObject(i)
                    var imgarr: JSONArray=  cur_jso.getJSONArray("images")
                    var b = Appart(
                        cur_jso.getLong("id"),
                        cur_jso.getString("description"),
                        cur_jso.getString("shortDesc"),
                        cur_jso.getInt("price"),
                        cur_jso.getString("address"),
                        cur_jso.getString("city"),
                        cur_jso.getString("postalCode"),
                        cur_jso.getString("country"),
                        0,
                        cur_jso.getString("state"),
                        imgarr.get(0).toString(),
                        cur_jso.getString("bailleur"),
                        cur_jso.getString("titre"),
                        cur_jso.getInt("nbchambers"),
                        cur_jso.getInt("nbbathrooms"),
                        cur_jso.getInt("nbBeds"),
                        mutableListOf<String>(),
                        cur_jso.getString("createdAt"),
                        cur_jso.getString("updatedAt"),
                        cur_jso.getInt("surface"),
                        mutableListOf<String>()
                    )
                    listapp.add(b)
                }
                var actID : String = jsoninfo.getString("@id")
                var last: String = jsoninfo.getString("hydra:last")
                var next: String = ""
                next = jsoninfo.getString("hydra:next")
                if (actID != last) {
                    btn.visibility = View.VISIBLE
                    spinner.visibility = View.GONE
                    textView.visibility = View.GONE
                    lv.visibility = View.VISIBLE
                    btn.setOnClickListener {
                        spinner.visibility = View.VISIBLE
                        textView.visibility = View.VISIBLE
                        btn.visibility = View.GONE
                        lv.visibility = View.GONE
                            var req4 : JsonObjectRequest = object : JsonObjectRequest(
                                Request.Method.GET,
                                "https://api.pariscaretakerservices.fr$next",
                                null,
                                Response.Listener{ content ->
                                    var jsonarr: JSONArray = content.getJSONArray("hydra:member")
                                    var jsoninfo: JSONObject = content.getJSONObject("hydra:view")
                                    for ( i in 0 .. jsonarr.length()-1){
                                        var cur_jso =jsonarr.getJSONObject(i)
                                        var imgarr: JSONArray=  cur_jso.getJSONArray("images")
                                        var b = Appart(
                                            cur_jso.getLong("id"),
                                            cur_jso.getString("description"),
                                            cur_jso.getString("shortDesc"),
                                            cur_jso.getInt("price"),
                                            cur_jso.getString("address"),
                                            cur_jso.getString("city"),
                                            cur_jso.getString("postalCode"),
                                            cur_jso.getString("country"),
                                            0,
                                            cur_jso.getString("state"),
                                            imgarr.get(0).toString(),
                                            cur_jso.getString("bailleur"),
                                            cur_jso.getString("titre"),
                                            cur_jso.getInt("nbchambers"),
                                            cur_jso.getInt("nbbathrooms"),
                                            cur_jso.getInt("nbBeds"),
                                            mutableListOf<String>(),
                                            cur_jso.getString("createdAt"),
                                            cur_jso.getString("updatedAt"),
                                            cur_jso.getInt("surface"),
                                            mutableListOf<String>()
                                        )
                                        listapp.add(b)
                                    }
                                    adapter = AppartAdapter(requireContext(), listapp)
                                    lv.adapter = adapter
                                    btn.visibility = View.VISIBLE
                                    lv.visibility = View.VISIBLE
                                    spinner.visibility = View.GONE
                                    textView.visibility = View.GONE
                                    actID = jsoninfo.getString("@id")
                                    last = jsoninfo.getString("hydra:last")
                                    try {
                                        next = jsoninfo.getString("hydra:next")
                                    } catch (e: JSONException){
                                        val snackbar = Snackbar.make(view, "No more appartements", Snackbar.LENGTH_LONG)
                                        snackbar.show()
                                        btn.visibility = View.GONE
                                        lv.visibility = View.VISIBLE
                                        spinner.visibility = View.GONE
                                        textView.visibility = View.GONE
                                    }
                                },
                                Response.ErrorListener { error ->
                                    if (error.networkResponse != null) {
                                        val statusCode = error.networkResponse.statusCode
                                        val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                                        Log.e("hhn", errorMessage)
                                        Toast.makeText(requireContext(), "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                                    } else {
                                        //Toast.makeText(requireContext(), error.message, Toast.LENGTH_LONG).show()
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
                            queue.add(req4)
                    }
                } else {
                    btn.setOnClickListener{
                        val snackbar = Snackbar.make(view, "No more appartements", Snackbar.LENGTH_LONG)
                        snackbar.show()
                        spinner.visibility = View.GONE
                        textView.visibility = View.GONE
                        btn.visibility = View.GONE
                        lv.visibility = View.VISIBLE
                    }
                }
                adapter = AppartAdapter(requireContext(), listapp)
                lv.adapter = adapter
                spinner.visibility = View.GONE
                textView.visibility = View.GONE
                btn.visibility = View.VISIBLE
                lv.visibility = View.VISIBLE

            },
            Response.ErrorListener { error ->
                if (error.networkResponse != null) {
                    val statusCode = error.networkResponse.statusCode
                    val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                    Log.e("hhn", errorMessage)
                    Toast.makeText(requireContext(), "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                } else {
                    //Toast.makeText(requireContext(), error.message, Toast.LENGTH_LONG).show()
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
            { content ->
                try {
                    token = content.getString("token")
                    val editor = requireContext().getSharedPreferences("user", Context.MODE_PRIVATE).edit()
                    editor.putString("token", token)
                    editor.apply()
                    queue.add(req3)
                } catch (e: JSONException){
                    e.printStackTrace()
                }
            },
            { error ->
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
            val item = adapter?.getItem(position) as Appart
            val bundle = Bundle()
            bundle.putLong("id", item.id)
            bundle.putString("shortDesc", item.shortDesc)
            bundle.putInt("price", item.price)
            bundle.putString("address", item.address)
            bundle.putString("postalCode", item.postalCode)
            bundle.putString("city", item.city)
            bundle.putString("country", item.country)
            bundle.putString("titre", item.titre)
            bundle.putString("image", item.image)
            bundle.putInt("nbchambers", item.nbchambers)
            bundle.putInt("nbbathrooms", item.nbbathrooms)
            bundle.putInt("nbBeds", item.nbBeds)
            bundle.putInt("surface", item.surface)
            findNavController().navigate(R.id.nav_appart, bundle)

        }

        super.onViewCreated(view, savedInstanceState)

    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }
}