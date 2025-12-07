import { motion } from 'framer-motion';
import { TrendingUp, Clock, Users, Award, CheckCircle } from 'lucide-react';

const features = [
  {
    icon: TrendingUp,
    title: 'Crescimento Acelerado',
    subtitle: 'Tecnologia que acompanha sua evolução',
    description: 'Implementamos soluções modernas e escaláveis — cloud, automações e infraestrutura — para que sua empresa cresça sem travar no caminho.',
    color: 'blue',
  },
  {
    icon: Clock,
    title: 'Resposta Rápida',
    subtitle: 'Atendimento ágil e eficiente',
    description: 'Suporte rápido via WhatsApp, atendimento remoto imediato e presença quando necessário. Nada de demora para resolver o que pode ser resolvido na hora.',
    color: 'cyan',
  },
  {
    icon: Users,
    title: 'Soluções sob Medida',
    subtitle: 'Serviços personalizados para cada necessidade',
    description: 'Nada de "pacote genérico". Cada projeto é adaptado à realidade da sua empresa: TI, automação, segurança, integrações, cloud, rotinas e sistemas.',
    color: 'purple',
  },
  {
    icon: Award,
    title: 'Foco em Segurança',
    subtitle: 'Proteção completa para seus dados e operação',
    description: 'Segurança digital aplicada de forma prática: configurações, antivírus corporativo, VPN, backups, boas práticas e prevenção de incidentes.',
    color: 'green',
  },
];

const colorClasses: Record<string, string> = {
  blue: 'from-blue-500 to-blue-600 shadow-blue-500/25',
  cyan: 'from-cyan-500 to-cyan-600 shadow-cyan-500/25',
  purple: 'from-purple-500 to-purple-600 shadow-purple-500/25',
  green: 'from-emerald-500 to-emerald-600 shadow-emerald-500/25',
};

export default function FeaturesSection() {
  return (
    <section className="relative py-24 bg-slate-950 overflow-hidden">
      {/* Background Elements */}
      <div className="absolute inset-0 bg-gradient-to-b from-slate-950 via-slate-900/50 to-slate-950" />
      <div className="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-blue-500/20 to-transparent" />

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="text-center mb-16"
        >
          <span className="inline-block px-4 py-1.5 bg-blue-500/10 border border-blue-500/20 rounded-full text-blue-400 text-sm font-medium mb-4">
            Por que nos escolher
          </span>
          <h2 className="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">
            Diferenciais que fazem a{' '}
            <span className="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
              diferença
            </span>
          </h2>
          <p className="text-slate-400 text-lg max-w-2xl mx-auto">
            Combinamos tecnologia, agilidade e atendimento consultivo para entregar 
            soluções que realmente resolvem problemas e impulsionam resultados.
          </p>
        </motion.div>

        {/* Features Grid */}
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
          {features.map((feature, index) => (
            <motion.div
              key={feature.title}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: index * 0.1 }}
              className="group relative"
            >
              <div className="relative h-full p-6 bg-slate-900/50 backdrop-blur-sm rounded-2xl border border-slate-800 hover:border-slate-700 transition-all duration-300 hover:-translate-y-1">
                {/* Icon */}
                <div className={`inline-flex p-3 rounded-xl bg-gradient-to-br ${colorClasses[feature.color]} shadow-lg mb-5`}>
                  <feature.icon className="w-6 h-6 text-white" />
                </div>

                <h3 className="text-xl font-semibold text-white mb-1 group-hover:text-blue-400 transition-colors">
                  {feature.title}
                </h3>
                <p className="text-sm text-blue-400/80 mb-3">
                  {feature.subtitle}
                </p>
                <p className="text-slate-400 leading-relaxed text-sm">
                  {feature.description}
                </p>

                {/* Hover Glow */}
                <div className="absolute -inset-px bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-cyan-500/0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity -z-10 blur-xl" />
              </div>
            </motion.div>
          ))}
        </div>

        {/* Trust Indicators */}
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.4 }}
          className="mt-16 p-8 bg-gradient-to-r from-slate-900/80 via-slate-800/50 to-slate-900/80 backdrop-blur-sm rounded-2xl border border-slate-800"
        >
          <div className="flex flex-col lg:flex-row items-center justify-between gap-8">
            <div className="flex items-center gap-4">
              <div className="p-3 bg-blue-500/10 rounded-xl">
                <CheckCircle className="w-8 h-8 text-blue-400" />
              </div>
              <div>
                <h4 className="text-white font-semibold text-lg">Compromisso com Resultados</h4>
                <p className="text-slate-400">Trabalhamos para que sua operação esteja sempre funcionando, entregando suporte e soluções com foco total em eficiência.</p>
              </div>
            </div>
            <div className="flex flex-wrap justify-center gap-6">
              {['Microsoft Partner', 'ISO 27001', 'LGPD Compliance'].map((badge) => (
                <div key={badge} className="px-4 py-2 bg-slate-800/50 rounded-lg border border-slate-700/50">
                  <span className="text-slate-300 text-sm font-medium">{badge}</span>
                </div>
              ))}
            </div>
          </div>
        </motion.div>
      </div>
    </section>
  );
}
