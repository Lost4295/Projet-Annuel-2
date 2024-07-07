package com.example.android

import android.app.DatePickerDialog
import android.app.Dialog
import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.Toast
import com.android.volley.toolbox.JsonObjectRequest
import com.android.volley.toolbox.Volley
import com.stripe.android.PaymentConfiguration
import com.stripe.android.paymentsheet.PaymentSheet
import com.stripe.android.paymentsheet.PaymentSheetResult
import org.json.JSONObject
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.navigation.fragment.findNavController
import com.example.android.databinding.FragmentLocationBinding
import com.example.android.databinding.FragmentReservationBinding
import java.text.SimpleDateFormat
import java.util.Calendar
import java.util.Locale

class ReservationFragment : Fragment() {

    private var _binding: FragmentReservationBinding? = null
    var paymentIntentClientSecret: String = ""
    private lateinit var paymentSheet: PaymentSheet
    lateinit var customerConfig: PaymentSheet.CustomerConfiguration
    lateinit var tvDateD : TextView
    lateinit var tvDateF : TextView
    lateinit var btnDateDebut : Button
    lateinit var btnDateFin : Button
    private val calendarD = Calendar.getInstance()
    private val calendarF = Calendar.getInstance()
    // This property is only valid between onCreateView and
    // onDestroyView.
    var req = false
    var period:Long = 0
    private val binding get() = _binding!!

    override fun onCreateView(
        inflater: LayoutInflater,
        container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View {
        _binding = FragmentReservationBinding.inflate(inflater, container, false)
        val root: View = binding.root
        paymentSheet = PaymentSheet(this, ::onPaymentSheetResult)
        tvDateD = root.findViewById(R.id.tv_dateD)
        tvDateF = root.findViewById(R.id.tv_dateF)
        btnDateDebut = root.findViewById(R.id.btn_dateD)
        btnDateFin = root.findViewById(R.id.btn_dateF)
        btnDateDebut.setOnClickListener {
            showDatePickerDialog(btnDateDebut, calendarD)
            period = checker()
        }
        btnDateFin.setOnClickListener {
            showDatePickerDialog(btnDateFin, calendarF)
            period = checker()
        }
        return root
    }
    private fun showDatePickerDialog(tvDate: Button, calendar: Calendar) {
        val datePickerDialog = DatePickerDialog(requireContext(), { DatePicker, year, month, dayOfMonth ->
            calendar.set(Calendar.YEAR, year)
            calendar.set(Calendar.MONTH, month)
            calendar.set(Calendar.DAY_OF_MONTH, dayOfMonth)
            val dateFormat = SimpleDateFormat("dd-MM-yyyy", Locale.getDefault())
            val date = dateFormat.format(calendar.time)
            tvDate.text = date
            tvDate.hint = ""
        }, calendar.get(Calendar.YEAR), calendar.get(Calendar.MONTH), calendar.get(Calendar.DAY_OF_MONTH))
        datePickerDialog.show()
    }

fun checker() : Long {
    if (btnDateDebut.text == "" || btnDateFin.text == "" || btnDateDebut.hint == "JJ/MM/AAAA" || btnDateFin.hint == "JJ/MM/AAAA") {
        return if (btnDateDebut.text == "" || btnDateDebut.hint == "JJ/MM/AAAA") {
            Toast.makeText(requireContext(), "Veuillez choisir la date de début", Toast.LENGTH_SHORT).show()
            1
        } else {
            Toast.makeText(requireContext(), "Veuillez choisir la date de fin", Toast.LENGTH_SHORT).show()
            1
        }
    } else {
        val dateD = SimpleDateFormat("dd-MM-yyyy", Locale.getDefault()).parse(btnDateDebut.text.toString())
        val dateF = SimpleDateFormat("dd-MM-yyyy", Locale.getDefault()).parse(btnDateFin.text.toString())
        if (dateD != null) {
           return (dateF.time - dateD.time)/(1000*60*60*24)
            req = true
        }
        }
     return 1
    }
    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        val btn = view.findViewById<Button>(R.id.btn_reserv)
        btn.setOnClickListener {
            Log.i("hhn1", btnDateDebut.text.toString())
            if (btnDateDebut.text == "" || btnDateFin.text == "" || btnDateDebut.hint == "JJ/MM/AAAA" || btnDateFin.hint == "JJ/MM/AAAA") {
                Toast.makeText(requireContext(), "Veuillez choisir les dates de début et de fin", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            } else {
            val dateD = SimpleDateFormat("dd-MM-yyyy", Locale.getDefault()).parse(btnDateDebut.text.toString())
            val dateF = SimpleDateFormat("dd-MM-yyyy", Locale.getDefault()).parse(btnDateFin.text.toString())
            if (dateD != null) {
                if (dateD.after(dateF)) {
                    Toast.makeText(
                        requireContext(),
                        "La date de début doit être inférieure à la date de fin",
                        Toast.LENGTH_SHORT
                    ).show()
                    return@setOnClickListener
                }
            }
            }
            val adults = view.findViewById<TextView>(R.id.et_adulte).text.toString()
            val children = view.findViewById<TextView>(R.id.et_enfant).text.toString()
            val babies = view.findViewById<TextView>(R.id.et_bebes).text.toString()
            if (adults == "" || children == "" || babies == "") {
                Toast.makeText(requireContext(), "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }
            val data = arguments
            val json = JSONObject()
            val shp = requireContext().getSharedPreferences("user", 0)
            Log.e("period", period.toString())
            if (!req) {
                period = checker()
            }
            json.put(
                "user", JSONObject().put("email", shp.getString("name", "example@example.com")).put("name", shp.getString("rname", "Example User"))
                    .put("city", data?.getString("city")).put("country", data?.getString("country")).put("postal_code", data?.getString("postalCode"))
                    .put("address", data?.getString("address"))
            ).put("appart", JSONObject().put("price", data?.getInt("price")?.times(period) ?: 200).put("shortDesc", data?.getString("shortDesc")))
            fetch(json)
            if (paymentIntentClientSecret != "") {
                paymentSheet.presentWithPaymentIntent(
                    paymentIntentClientSecret,
                    PaymentSheet.Configuration(
                        merchantDisplayName = "Paris Caretaker Services",
                        customer = customerConfig,
                        allowsDelayedPaymentMethods = false
                    )
                )
            } else {
                Toast.makeText(requireContext(), "PaymentIntentClientSecret is not initialized. Please wait for loading...", Toast.LENGTH_SHORT).show()
            }

        }

    }


    private fun onPaymentSheetResult(paymentSheetResult: PaymentSheetResult) {
        when (paymentSheetResult) {
            is PaymentSheetResult.Completed-> {
                fetch()
                val data = arguments
                val queue = Volley.newRequestQueue(requireContext())
                val shp = requireContext().getSharedPreferences("user", 0)
                val json = JSONObject()
                json.put("dateha", SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(Calendar.getInstance().time)+"T00:00:00Z")
                json.put("dateDebut",SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(calendarD.time)+"T00:00:00Z")
                json.put("dateFin",SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(calendarF.time)+"T00:00:00Z")
                json.put("appartement", "/appartements/" + data?.getString("id"))
                json.put("locataire",  shp.getString("id", ""))
                json.put("adults", requireView().findViewById<TextView>(R.id.et_adulte).text.toString().toInt())
                json.put("kids", requireView().findViewById<TextView>(R.id.et_enfant).text.toString().toInt())
                json.put("babies", requireView().findViewById<TextView>(R.id.et_bebes).text.toString().toInt())
                json.put("price", data?.getInt("price")?.times(period))
                val request:JsonObjectRequest = object : JsonObjectRequest(
                    Method.POST,
                    "https://api.pariscaretakerservices.fr/loca",
                    json,
                    {
                        Toast.makeText(requireContext(), "Payment completed", Toast.LENGTH_SHORT).show()
                        findNavController().navigate(R.id.nav_home)
                    },
                    { error ->
                        if (error.networkResponse != null) {
                            val statusCode = error.networkResponse.statusCode
                            val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                            Log.e("hhn", errorMessage)
                            Toast.makeText(requireContext(), "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                        } else {
                            Toast.makeText(requireContext(), error.toString(), Toast.LENGTH_LONG).show()
                            Log.e("herhdertfee", error.toString())
                        }
                        error.printStackTrace()
                    }){
                    override fun getHeaders(): MutableMap<String, String> {
                        val params = HashMap<String, String>()
                        val shp = requireContext().getSharedPreferences("user", 0)
                        params["Accept"] = "*/*"
                        params["Content-Type"] = "application/json"
                        return params
                    }
                }
                queue.add(request)
            }
            is PaymentSheetResult.Canceled -> {
                Toast.makeText(requireContext(), "Payment canceled", Toast.LENGTH_SHORT).show()
            }
            is PaymentSheetResult.Failed -> {
                Toast.makeText(requireContext(), "Payment failed : ${paymentSheetResult.error.message}", Toast.LENGTH_SHORT).show()
            }
        }
    }
    private fun fetch(jsonObject: JSONObject? = null){
        val queue = Volley.newRequestQueue(requireContext())
        val json: JSONObject
        if (jsonObject != null) {
            json = jsonObject
        } else {
            json = JSONObject()
            json.put(
                "user", JSONObject().put("email", "example@example.com").put("name", "Example User")
                    .put("city", "Paris").put("country", "France").put("postal_code", "75001")
                    .put("address", "1 rue de la paix")
            ).put("appart", JSONObject().put("price", 520).put("shortDesc", "Appartement 2 pièces"))
        }
        val request:JsonObjectRequest = object : JsonObjectRequest(
            Method.POST,
            "https://api.pariscaretakerservices.fr/create-payment-intent",
            json,
            { response ->
                paymentIntentClientSecret = response.getString("paymentIntent")
                customerConfig = PaymentSheet.CustomerConfiguration(
                    response.getString("customer"),
                    response.getString("ephemeralKey")
                )
                val publishableKey = response.getString("publishableKey")
                PaymentConfiguration.init(requireContext(), publishableKey)
            },
            { error ->
                if (error.networkResponse != null) {
                    val statusCode = error.networkResponse.statusCode
                    val errorMessage = String(error.networkResponse.data, charset("UTF-8"))
                    Log.e("hhn", errorMessage)
                    Toast.makeText(requireContext(), "Error: $statusCode - $errorMessage", Toast.LENGTH_LONG).show()
                } else {
                    Toast.makeText(requireContext(), error.toString(), Toast.LENGTH_LONG).show()
                    Log.e("herhdertfee", error.toString())
                }
                error.printStackTrace()
            }){
            override fun getHeaders(): MutableMap<String, String> {
                val params = HashMap<String, String>()
                params["Content-Type"] = "application/json"
                return params
            }
        }
        queue.add(request)

    }
}