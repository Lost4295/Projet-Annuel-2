package com.example.android.ui.data

import android.content.Context
import android.util.Log
import android.view.View
import android.widget.TextView
import android.widget.Toast
import androidx.navigation.findNavController
import com.android.volley.AuthFailureError
import com.android.volley.Request
import com.android.volley.Response
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.example.android.R
import com.example.android.ui.data.model.LoggedInUser
import org.json.JSONException
import org.json.JSONObject
import java.io.IOException

/**
 * Class that handles authentication w/ login credentials and retrieves user information.
 */
class LoginDataSource {

    fun login(username: String, password: String, context: Context, view: View): Result<LoggedInUser> {
        try {
            // TODO: handle loggedInUser authentication
            var queue = Volley.newRequestQueue(context)
            var token = ""
            var id = ""
            var name= ""
            var msg: String
            val jsonBody = JSONObject()
            try {
                jsonBody.put("email", username)
                jsonBody.put("password", password)
            } catch (e: JSONException) {
                e.printStackTrace()
            }
            val json2 = JSONObject()
            json2.put("Authorization", "Bearer $token")
            val req2 : JsonObjectRequest = object : JsonObjectRequest(
                Request.Method.GET,
                "https://api.pariscaretakerservices.fr/user/",
                json2,
                Response.Listener{ content ->
                    try {
                        val cur_jso =content.getJSONObject("data")
                        if (cur_jso.getString("email") == username){
                            id = cur_jso.getString("id")
                            name = cur_jso.getString("email")
                            val realusername = cur_jso.getString("firstname")
                            val editor = context.getSharedPreferences("user", Context.MODE_PRIVATE).edit()
                            editor.putString("usrtoken", token)
                            editor.putString("id", id)
                            editor.putString("name", name)
                            editor.putString("rname", realusername)
                            editor.putString("phone", cur_jso.getString("phone"))
                            editor.putString("birthdate", cur_jso.getString("birthdate"))
                            editor.putString("abonnement", cur_jso.getString("abonnement"))
                            editor.putString("namer", cur_jso.getString("name"))
                            editor.apply()
                            Toast.makeText(context, "Bienvenue sur le site de PCS, $realusername !", Toast.LENGTH_LONG).show()
                            view.findNavController().navigate(R.id.nav_home)

                        } else {
                            Toast.makeText(context, "Error: Data ; username = $username, name = $name", Toast.LENGTH_LONG).show()
                        }
                    if (id == ""){
                        Toast.makeText(context, "Error: User not found", Toast.LENGTH_LONG).show()
                    }
                    } catch (e: JSONException){
                        e.printStackTrace()
                    }
                },
                Response.ErrorListener { error ->
                    if (error.networkResponse != null) {
                        val statusCode = error.networkResponse.statusCode
                        val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                        Toast.makeText(context, "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                    } else {
                        Toast.makeText(context, error.toString(), Toast.LENGTH_LONG).show()
                    }
                    Log.e("herhe", error.toString())
                }
            ){
                @Throws(AuthFailureError::class)
                override fun getHeaders(): Map<String, String> {
                    var params: MutableMap<String, String> = mutableMapOf()
                    params.put("Authorization","Bearer $token")
                    params.put("Accept","*/*")
                    params.put("Content-Type","application/ld+json")
                    //..add other headers
                    return params
                }
            };
            var auth = JsonObjectRequest(
                Request.Method.POST,
                "https://api.pariscaretakerservices.fr/auth",
                jsonBody,
                { content ->
                    try {
                        token = content.getString("token")
                        queue.add(req2)
                    } catch (e: JSONException){
                        e.printStackTrace()
                    }
                },
                { error ->
                    if (error.networkResponse != null) {
                        val statusCode = error.networkResponse.statusCode
                        if (statusCode == 401) {
                            msg = "Vos identifiants sont incorrects. Merci de réessayer."
                            Toast.makeText(context, msg, Toast.LENGTH_LONG).show()
                        } else {
                            val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                            Toast.makeText(context, "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                        }
                       // Toast.makeText(context, "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                    } else {
                        msg = "Une erreur est survenue. Merci de réessayer plus tard."
                        Toast.makeText(context, msg, Toast.LENGTH_LONG).show()
                    }
                    //Log.e("herhe", error.toString())
                    //Log.e("herhem", String(error.networkResponse.data, charset("UTF-8")))
                }
            )

            queue.add(auth)
            val fakeUser = LoggedInUser("5", "014")
            return Result.Success(fakeUser)
        } catch (e: Throwable) {
            return Result.Error(IOException("Error logging in", e))
        }
    }

    fun logout() {
        // TODO: revoke authentication
    }
}
